import { Media } from '@/Features/Media';
import { NewsCategory } from '@/Features/NewsCategory';

export type NewsStatus = 'draft' | 'willBePublished' | 'published';

export type News = {
  id: number;
  status: NewsStatus;
  title: string;
  slug: string;
  content: string;
  published_at?: string;
  category: NewsCategory;
  eyecatch?: Media;
};

export type NewsFormData = {
  status: NewsStatus;
  title: string;
  slug: string;
  content: string;
  published_at?: string;
  category_id: string;
  eyecatch?: File | null;
};

export type NewsPreview = {
  url: string;
};
