import { TreeItem } from '@/UI/Types';

export type Page = {
  name: string;
  href?: string;
};

export type Pages = TreeItem<Page>[];
