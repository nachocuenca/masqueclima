import type { NextConfig } from "next";

const isProduction = process.env.APP_ENV === "production";

const nextConfig: NextConfig = {
  output: "standalone",
  trailingSlash: true,
  poweredByHeader: false,
  reactStrictMode: true,
  images: {
    unoptimized: true
  },
  async headers() {
    if (isProduction) {
      return [];
    }

    return [
      {
        source: "/:path*",
        headers: [
          {
            key: "X-Robots-Tag",
            value: "noindex, nofollow, noarchive",
          },
        ],
      },
    ];
  },
};

export default nextConfig;
