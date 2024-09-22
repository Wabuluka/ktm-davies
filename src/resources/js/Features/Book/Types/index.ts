import { User } from '@/Features/Auth';
import { Benefit } from '@/Features/Benefit';
import { Block, BlockOnBookForm } from '@/Features/Block/Types';
import { BookStoreOnBookForm } from '@/Features/BookBookStore/Types';
import { Creation, CreationOnBookForm } from '@/Features/BookCreation/Types';
import { EbookStoreOnBookForm } from '@/Features/BookEbookStore/Types';
import { Character } from '@/Features/Character/Types';
import { Creator } from '@/Features/Creator';
import { Genre } from '@/Features/Genre/Types';
import { Label } from '@/Features/Label/Types';
import { Media } from '@/Features/Media';
import {
  RelatedItem,
  RelatedItemOnBookForm,
} from '@/Features/RelatedItem/Types';
import { Series } from '@/Features/Series/Types';
import { Site } from '@/Features/Site/Types';
import { Store } from '@/Features/Store';
import { Story } from '@/Features/Story/Types';

export type BookStatus = 'draft' | 'willBePublished' | 'published';

export type Book = {
  id: number;
  status: BookStatus;
  title: string;
  title_kana?: string;
  volume?: string;
  caption?: string;
  description?: string;
  keywords?: string;
  isbn13?: string;
  price?: number;
  release_date?: string;
  published_at?: string;
  format_id?: number;
  size_id?: number;
  ebook_only: boolean;
  special_edition: boolean;
  limited_edition: boolean;
  adult: boolean;
  trial_url?: string;
  survey_url?: string;
  cover?: Media;
  label?: Label;
  genre?: Genre;
  series?: Series;
  characters: Character[];
  creators: Array<
    Creator & {
      creation: Creation;
    }
  >;
  benefits?: Benefit[];
  bookstores: Array<
    Store & {
      pivot: {
        url: string;
        is_primary: boolean;
      };
    }
  >;
  ebookstores: Array<
    Store & {
      pivot: {
        url: string;
        is_primary: boolean;
      };
    }
  >;
  stories?: Story[];
  related_items: RelatedItem[];
  blocks: Block[];
  sites: Site[];
  updated_at: string;
  updatedBy?: User;
};

export type BookFormData = {
  status: BookStatus;
  title: string;
  title_kana: string;
  volume: string;
  caption: string;
  description: string;
  keywords: string;
  isbn13: string;
  price: string;
  release_date?: string;
  published_at?: string;
  ebook_only: boolean;
  special_edition: boolean;
  limited_edition: boolean;
  adult: boolean;
  trial_url: string;
  survey_url: string;
  cover?: File | null;
  label_id: string;
  genre_id: string;
  series_id: string;
  format_id: string;
  size_id: string;
  characters: Character[];
  creations: CreationOnBookForm[];
  bookstores: BookStoreOnBookForm[];
  ebookstores: EbookStoreOnBookForm[];
  benefits: Benefit[];
  stories: Story[];
  related_items: {
    upsert: RelatedItemOnBookForm[];
    deleteIds: RelatedItemOnBookForm['id'][];
  };
  blocks: {
    upsert: BlockOnBookForm[];
    deleteIds: BlockOnBookForm['id'][];
  };
  sites: string[];
};

export type BookPreview = {
  site: Site;
  url: string;
};
