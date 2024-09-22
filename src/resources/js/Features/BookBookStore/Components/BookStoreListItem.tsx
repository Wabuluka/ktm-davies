import { BookStoreOnBookForm } from '@/Features/BookBookStore/Types';
import { BookStore, useBookStore } from '@/Features/BookStore';
import { EditButton } from '@/UI/Components/Form/Button/EditButton';
import { DeleteIcon } from '@chakra-ui/icons';
import { Center, HStack, IconButton, Radio, Td, Tr } from '@chakra-ui/react';

type Props = {
  bookStore: BookStoreOnBookForm;
  selectType?: 'radio' | 'none';
  onEdit: (bookStore: BookStoreOnBookForm) => void;
  onDelete: (bookStore: BookStoreOnBookForm) => void;
};

export function BookStoreListItem({
  bookStore,
  selectType = 'none',
  onEdit,
  onDelete,
}: Props) {
  const storeName = (useBookStore(bookStore.id) as BookStore).store.name;

  function handleEditButtonClick() {
    onEdit(bookStore);
  }
  function handleDeleteButtonClick() {
    onDelete(bookStore);
  }

  return (
    <Tr>
      {selectType !== 'none' && (
        <Td p={0}>
          <Center>
            <Radio
              value={bookStore.id}
              isChecked={bookStore.is_primary}
              size="lg"
            />
          </Center>
        </Td>
      )}
      <Td>{storeName}</Td>
      <Td>{bookStore.url}</Td>
      <Td>
        <HStack>
          <EditButton
            aria-label={`Edit ${storeName}`}
            onClick={handleEditButtonClick}
          />
          <IconButton
            as={DeleteIcon}
            aria-label={`Delete ${storeName}`}
            onClick={handleDeleteButtonClick}
            bg="red.500"
            color="white"
            p={2}
          />
        </HStack>
      </Td>
    </Tr>
  );
}
