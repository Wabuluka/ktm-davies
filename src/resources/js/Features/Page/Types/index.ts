import { Site } from '@/Features/Site/Types';

export type Page = {
  id: number;
  title: string;
  slug: string;
  content: string;
  site: Site;
};

export type PagePreview = {
  url: string;
};
