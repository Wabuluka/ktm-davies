import { Book, BookFormData } from '../../Types';

type Return = Pick<BookFormData, 'status' | 'published_at' | 'sites'>;

export function createPublicationFieldInitialValues(
  book: Book | null = null,
): Return {
  return {
    status: book?.status || 'draft',
    published_at: book?.published_at || '',
    sites: book?.sites.map((site) => site.id.toString()) || [],
  };
}
