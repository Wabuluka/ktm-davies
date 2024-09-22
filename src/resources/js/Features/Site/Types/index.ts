import { BannerPlacement } from '@/Features/Banner';
import { Media } from '@/Features/Media';
import { NewsCategory } from '@/Features/NewsCategory';
import { Page } from '@/Features/Page/Types';

export type Site = {
  id: number;
  name: string;
  url: string;
  book_preview_path?: string;
  news_preview_path?: string;
  page_preview_path?: string;
  logo?: Media;
  pages?: Omit<Page, 'site'>[];
  bannerPlacements: BannerPlacement[];
  news_categories: NewsCategory[];
};
