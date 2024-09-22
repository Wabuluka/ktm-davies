import { uniqueId } from 'lodash';
import { Book, BookFormData } from '../../Types';

type Return = Pick<
  BookFormData,
  | 'bookstores'
  | 'ebookstores'
  | 'benefits'
  | 'related_items'
  | 'stories'
  | 'characters'
  | 'blocks'
>;

const defaultBlocks: Return['blocks'] = {
  upsert: [
    {
      id: `+${uniqueId()}`,
      type_id: '1',
      sort: 1,
      displayed: true,
    },
    {
      id: `+${uniqueId()}`,
      type_id: '2',
      sort: 2,
      displayed: false,
    },
    {
      id: `+${uniqueId()}`,
      type_id: '3',
      sort: 3,
      displayed: false,
    },
    {
      id: `+${uniqueId()}`,
      type_id: '4',
      sort: 4,
      displayed: false,
    },
    {
      id: `+${uniqueId()}`,
      type_id: '5',
      sort: 5,
      displayed: false,
    },
    {
      id: `+${uniqueId()}`,
      type_id: '6',
      sort: 6,
      displayed: false,
    },
    {
      id: `+${uniqueId()}`,
      type_id: '7',
      sort: 7,
      displayed: false,
    },
    {
      id: `+${uniqueId()}`,
      type_id: '8',
      sort: 8,
      displayed: false,
    },
  ],
  deleteIds: [],
};

export function createDisplayConfigFieldInitialValues(
  book: Book | null = null,
): Return {
  const benefits = book?.benefits ?? [];
  const bookstores =
    book?.bookstores.map((sotre) => ({
      id: String(sotre.id),
      url: sotre.pivot.url,
      is_primary: sotre.pivot.is_primary,
    })) || [];
  const ebookstores =
    book?.ebookstores.map((store) => ({
      id: String(store.id),
      url: store.pivot.url,
      is_primary: store.pivot.is_primary,
    })) || [];
  const stories = book?.stories ?? [];
  const characters = book?.characters ?? [];
  const related_items = book
    ? {
        upsert:
          book.related_items.map(
            ({ id, relatable_id, relatable_type, description, sort }) => ({
              id: String(id),
              relatable_id: String(relatable_id),
              relatable_type,
              description,
              sort,
            }),
          ) || [],
        deleteIds: [],
      }
    : {
        upsert: [],
        deleteIds: [],
      };
  const blocks = book
    ? {
        upsert:
          book.blocks.map(
            ({
              id,
              type_id,
              custom_title,
              custom_content,
              sort,
              displayed,
            }) => ({
              id: String(id),
              type_id: String(type_id),
              custom_title,
              custom_content,
              sort,
              displayed,
            }),
          ) || [],
        deleteIds: [],
      }
    : defaultBlocks;

  return {
    benefits,
    bookstores,
    ebookstores,
    related_items,
    stories,
    characters,
    blocks,
  };
}
