export type GoogleBusinessReview = {
  id: string;
  authorName: string;
  authorPhotoUrl?: string;
  rating: number;
  comment: string;
  createTime?: string;
  updateTime?: string;
};

export type GoogleBusinessReviews = {
  averageRating: number;
  totalReviewCount: number;
  reviewPageUrl?: string;
  reviews: GoogleBusinessReview[];
};

type TokenResponse = {
  access_token?: string;
  expires_in?: number;
  error?: string;
  error_description?: string;
};

type ApiReview = {
  name?: string;
  reviewId?: string;
  reviewer?: {
    profilePhotoUrl?: string;
    displayName?: string;
    isAnonymous?: boolean;
  };
  starRating?: "ONE" | "TWO" | "THREE" | "FOUR" | "FIVE" | "STAR_RATING_UNSPECIFIED";
  comment?: string;
  createTime?: string;
  updateTime?: string;
};

type ReviewsResponse = {
  reviews?: ApiReview[];
  averageRating?: number;
  totalReviewCount?: number;
  nextPageToken?: string;
};

const ratingMap = {
  ONE: 1,
  TWO: 2,
  THREE: 3,
  FOUR: 4,
  FIVE: 5,
  STAR_RATING_UNSPECIFIED: 0
} as const;

function googleBusinessEnv() {
  return {
    accountId: process.env.GOOGLE_BUSINESS_ACCOUNT_ID,
    locationId: process.env.GOOGLE_BUSINESS_LOCATION_ID,
    clientId: process.env.GOOGLE_BUSINESS_CLIENT_ID,
    clientSecret: process.env.GOOGLE_BUSINESS_CLIENT_SECRET,
    refreshToken: process.env.GOOGLE_BUSINESS_REFRESH_TOKEN,
    reviewPageUrl: process.env.GOOGLE_BUSINESS_REVIEW_PAGE_URL
  };
}

function hasGoogleBusinessConfig() {
  const env = googleBusinessEnv();
  return Boolean(
    env.accountId &&
      env.locationId &&
      env.clientId &&
      env.clientSecret &&
      env.refreshToken
  );
}

async function getGoogleAccessToken(): Promise<string | null> {
  const env = googleBusinessEnv();

  if (!env.clientId || !env.clientSecret || !env.refreshToken) {
    return null;
  }

  const response = await fetch("https://oauth2.googleapis.com/token", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded"
    },
    body: new URLSearchParams({
      client_id: env.clientId,
      client_secret: env.clientSecret,
      refresh_token: env.refreshToken,
      grant_type: "refresh_token"
    }),
    cache: "no-store"
  });

  const data = (await response.json()) as TokenResponse;
  if (!response.ok || !data.access_token) {
    console.warn("Google Business token refresh failed", {
      status: response.status,
      error: data.error,
      description: data.error_description
    });
    return null;
  }

  return data.access_token;
}

export async function getGoogleBusinessReviews(): Promise<GoogleBusinessReviews | null> {
  if (!hasGoogleBusinessConfig()) {
    return null;
  }

  const env = googleBusinessEnv();
  const token = await getGoogleAccessToken();
  if (!token || !env.accountId || !env.locationId) {
    return null;
  }

  const parent = `accounts/${env.accountId}/locations/${env.locationId}`;
  const url = new URL(`https://mybusiness.googleapis.com/v4/${parent}/reviews`);
  url.searchParams.set("pageSize", "6");
  url.searchParams.set("orderBy", "updateTime desc");

  const response = await fetch(url, {
    headers: {
      Authorization: `Bearer ${token}`
    },
    next: {
      revalidate: 3600
    }
  });

  if (!response.ok) {
    console.warn("Google Business reviews fetch failed", {
      status: response.status,
      body: await response.text()
    });
    return null;
  }

  const data = (await response.json()) as ReviewsResponse;

  if (!data.averageRating || !data.totalReviewCount) {
    return null;
  }

  return {
    averageRating: data.averageRating,
    totalReviewCount: data.totalReviewCount,
    reviewPageUrl: env.reviewPageUrl,
    reviews:
      data.reviews?.map((review) => ({
        id: review.reviewId || review.name || `${review.reviewer?.displayName}-${review.updateTime}`,
        authorName: review.reviewer?.isAnonymous
          ? "Google"
          : review.reviewer?.displayName || "Google",
        authorPhotoUrl: review.reviewer?.profilePhotoUrl,
        rating: ratingMap[review.starRating || "STAR_RATING_UNSPECIFIED"],
        comment: review.comment || "",
        createTime: review.createTime,
        updateTime: review.updateTime
      })) ?? []
  };
}
