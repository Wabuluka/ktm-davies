import { Book } from '@/Features/Book/Types';
import { ExternalLink } from '@/Features/ExternalLink';
import { Media } from '@/Features/Media';

export type RelatebleType = 'book' | 'externalLink';

export type RelatedItem = {
  id: number;
  relatable_id: number;
  description: string;
  sort: number;
  thumbnail?: Media;
} & (
  | {
      relatable_type: 'book';
      relatable: Book;
    }
  | {
      relatable_type: 'externalLink';
      relatable: ExternalLink;
    }
);

export type RelatedItemFormData = {
  relatable_type: RelatebleType;
  relatable_id: string;
  description: RelatedItem['description'];
};

export type RelatedItemOnBookForm = RelatedItemFormData & {
  id: string;
  sort: number;
};
