import { Book, BookFormData } from '../../Types';

type Return = Pick<BookFormData, 'title' | 'title_kana' | 'volume'>;

export function createBookNameFieldInitialValues(
  book: Book | null = null,
): Return {
  return {
    title: book?.title || '',
    title_kana: book?.title_kana || '',
    volume: book?.volume || '',
  };
}
