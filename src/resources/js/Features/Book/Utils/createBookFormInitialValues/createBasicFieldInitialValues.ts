import { Book, BookFormData } from '../../Types';

type Return = Pick<
  BookFormData,
  | 'cover'
  | 'label_id'
  | 'genre_id'
  | 'series_id'
  | 'creations'
  | 'isbn13'
  | 'release_date'
  | 'price'
  | 'format_id'
  | 'size_id'
  | 'special_edition'
  | 'limited_edition'
  | 'ebook_only'
  | 'adult'
>;

export function createBasicFieldInitialValues(
  book: Book | null = null,
): Return {
  return {
    cover: undefined,
    label_id: book?.label?.id.toString() || '',
    genre_id: book?.genre?.id.toString() || '',
    series_id: book?.series?.id.toString() || '',
    creations:
      book?.creators.map((creator) => ({
        creator_id: String(creator.id),
        creation_type: creator.creation.creation_type,
        displayed_type: creator.creation.displayed_type,
        sort: creator.creation.sort,
      })) ?? [],

    isbn13: book?.isbn13 || '',
    release_date: book?.release_date || '',
    price: book?.price ? book?.price.toString() : '',
    format_id: book?.format_id?.toString() || '',
    size_id: book?.size_id?.toString() || '',
    special_edition: book?.special_edition || false,
    limited_edition: book?.limited_edition || false,
    ebook_only: book?.ebook_only || false,
    adult: book ? book.adult : true,
  };
}
