/* eslint-disable @next/next/no-img-element */
import Script from "next/script";
import { getGoogleBusinessReviews } from "@/lib/google-business";
import type { HomeDictionary, Lang } from "@/content/types";

type ReviewsProps = {
  dictionary: HomeDictionary;
  lang: Lang;
};

function Stars({ rating }: { rating: number }) {
  return (
    <span className="text-2xl tracking-[0.12em] text-[#f5b642]" aria-label={`${rating}/5`}>
      {"★★★★★".slice(0, Math.round(rating))}
    </span>
  );
}

function formatDate(date: string | undefined, lang: Lang): string | null {
  if (!date) {
    return null;
  }

  return new Intl.DateTimeFormat(lang, {
    month: "short",
    year: "numeric"
  }).format(new Date(date));
}

export async function Reviews({ dictionary, lang }: ReviewsProps) {
  const googleReviews = await getGoogleBusinessReviews();
  const hasGoogleCards = Boolean(googleReviews?.reviews.length);
  const averageRating = googleReviews?.averageRating ?? 5;

  return (
    <section className="bg-neutral-950 py-20 text-white" id="opiniones">
      <div className="section-shell">
        <div className="grid gap-8 lg:grid-cols-[0.55fr_1.45fr] lg:items-start">
          <div>
            <p className="inline-flex rounded-full bg-[#f5b642] px-4 py-2 text-sm font-black text-neutral-950">
              {averageRating.toFixed(1)}/5 Google
              {googleReviews?.totalReviewCount ? ` · ${googleReviews.totalReviewCount}` : ""}
            </p>
            <h2 className="mt-5 text-4xl font-black leading-tight">{dictionary.reviews.title}</h2>
            <p className="mt-5 leading-8 text-neutral-200">
              {[dictionary.reviews.desc, dictionary.reviews.helper].filter(Boolean).join(" ")}
            </p>
            <a
              className="focus-ring mt-8 inline-flex rounded-full bg-white px-6 py-3 font-bold text-neutral-950 transition hover:bg-[#f5b642]"
              href={googleReviews?.reviewPageUrl || "#contacto"}
              rel={googleReviews?.reviewPageUrl ? "noopener noreferrer" : undefined}
              target={googleReviews?.reviewPageUrl ? "_blank" : undefined}
            >
              {dictionary.reviews.cta}
            </a>
          </div>

          {hasGoogleCards && googleReviews ? (
            <div className="grid gap-4 md:grid-cols-2">
              {googleReviews.reviews.map((review) => (
                <article
                  className="min-h-64 rounded-lg bg-white p-6 text-neutral-950 shadow-xl shadow-black/15"
                  key={review.id}
                >
                  <div className="flex items-center gap-4">
                    {review.authorPhotoUrl ? (
                      <img
                        alt=""
                        className="h-12 w-12 rounded-full object-cover"
                        height={48}
                        loading="lazy"
                        src={review.authorPhotoUrl}
                        width={48}
                      />
                    ) : (
                      <div className="flex h-12 w-12 items-center justify-center rounded-full bg-teal-700 font-black text-white">
                        G
                      </div>
                    )}
                    <div>
                      <h3 className="font-black">{review.authorName}</h3>
                      <p className="text-sm text-neutral-500">
                        {formatDate(review.updateTime || review.createTime, lang)}
                      </p>
                    </div>
                  </div>
                  <div className="mt-4">
                    <Stars rating={review.rating} />
                  </div>
                  {review.comment ? (
                    <p className="mt-4 line-clamp-6 leading-7 text-neutral-700">
                      {review.comment}
                    </p>
                  ) : null}
                </article>
              ))}
            </div>
          ) : (
            <div className="rounded-lg bg-white p-5 text-neutral-950 shadow-xl shadow-black/15">
              <Script src="https://static.elfsight.com/platform/platform.js" strategy="lazyOnload" />
              <div
                className="elfsight-app-49da578d-ec47-4904-b0c4-91404d9d1b7e min-h-72"
                data-elfsight-app-lazy
              />
            </div>
          )}
        </div>
      </div>
    </section>
  );
}
