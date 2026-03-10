import type { NextConfig } from 'next';

const apiBase = process.env.NEXT_PUBLIC_API_BASE_URL ?? 'http://localhost:8000';
const apiOrigin = new URL(apiBase);

const nextConfig: NextConfig = {
  images: {
    dangerouslyAllowLocalIP: process.env.NODE_ENV === 'development',
    remotePatterns: [
      {
        protocol: apiOrigin.protocol.replace(':', '') as 'http' | 'https',
        hostname: apiOrigin.hostname,
        port: apiOrigin.port || '',
        pathname: '/api/public/media/**',
      },
      {
        protocol: 'http',
        hostname: 'localhost',
        port: '8000',
        pathname: '/api/public/media/**',
      },
    ],
  },
};

export default nextConfig;
