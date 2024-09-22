import { uniqueId } from 'lodash';
import { Book, BookFormData } from '../../Types';
import { createBookNameFieldInitialValues } from './createBookNameFieldInitialValues';
import { createPublicationFieldInitialValues } from './createPublicationFieldInitialValues';
import { createBasicFieldInitialValues } from './createBasicFieldInitialValues';
import { createDetailFieldInitialValues } from './createDetailFieldInitialValues';
import { createDisplayConfigFieldInitialValues } from './createDisplayConfigFieldInitialValues';

type Props =
  | { type: 'create'; book?: null }
  | { type: 'copy'; book: Book }
  | { type: 'edit'; book: Book };

export function createBookFormInitialValues({
  type,
  book,
}: Props): BookFormData {
  switch (type) {
    case 'create': {
      return {
        ...createBookNameFieldInitialValues(),
        ...createPublicationFieldInitialValues(),
        ...createBasicFieldInitialValues(),
        ...createDetailFieldInitialValues(),
        ...createDisplayConfigFieldInitialValues(),
      };
    }
    case 'copy': {
      const { title, ...bookNameFields } =
        createBookNameFieldInitialValues(book);
      const {
        related_items: relatedItemsBase,
        blocks: blocksBase,
        ...displayConfigFields
      } = createDisplayConfigFieldInitialValues(book);
      const related_items = {
        upsert: relatedItemsBase.upsert.map((related_item) => ({
          ...related_item,
          id: `+${uniqueId()}`,
        })),
        deleteIds: relatedItemsBase.deleteIds,
      };
      const blocks = {
        upsert: blocksBase.upsert.map((block) => ({
          ...block,
          id: `+${uniqueId()}`,
        })),
        deleteIds: blocksBase.deleteIds,
      };
      return {
        title: `${title.slice(0, 249)} (コピー)`,
        ...bookNameFields,
        ...createPublicationFieldInitialValues(book),
        ...createBasicFieldInitialValues(book),
        ...createDetailFieldInitialValues(book),
        ...displayConfigFields,
        related_items,
        blocks,
      };
    }
    case 'edit':
      return {
        ...createBookNameFieldInitialValues(book),
        ...createPublicationFieldInitialValues(book),
        ...createBasicFieldInitialValues(book),
        ...createDetailFieldInitialValues(book),
        ...createDisplayConfigFieldInitialValues(book),
      };
  }
}
