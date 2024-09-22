import { BookStoreListItem } from '@/Features/BookBookStore/Components/BookStoreListItem';
import { EditBookStoreDrawer } from '@/Features/BookBookStore/Components/EditBookStoreDrawer';
import {
  useBookStores,
  useBookStoresDispatch,
} from '@/Features/BookBookStore/Contexts/BookStoreDrawerContext';
import { BookStoreOnBookForm } from '@/Features/BookBookStore/Types';
import { Table, Tbody, Th, Thead, Tr, useDisclosure } from '@chakra-ui/react';
import { useState } from 'react';

type Props = {
  selectType?: 'radio' | 'none';
};

export function BookStoreList({ selectType = 'none' }: Props) {
  const { isOpen, onClose, onOpen } = useDisclosure();
  const [editingStore, setEditingStore] = useState<BookStoreOnBookForm>();
  const { bookstores } = useBookStores();
  const dispatch = useBookStoresDispatch();

  function handleEditButtonClick(bookStore: BookStoreOnBookForm) {
    setEditingStore(bookStore);
    onOpen();
  }
  function handleDeleteButtonClick({ id }: BookStoreOnBookForm) {
    dispatch?.({ type: 'delete', id });
  }

  return (
    <>
      <Table>
        <Thead>
          <Tr>
            {selectType !== 'none' && (
              <Th w={1} whiteSpace="nowrap">
                Show in List
              </Th>
            )}
            <Th>Purchase Options(Physical book)</Th>
            <Th>URL</Th>
            <Th w={1}>Operation</Th>
          </Tr>
        </Thead>
        <Tbody>
          {bookstores?.map((bookStore) => (
            <BookStoreListItem
              key={bookStore.id}
              bookStore={bookStore}
              selectType={selectType}
              onEdit={handleEditButtonClick}
              onDelete={handleDeleteButtonClick}
            />
          ))}
        </Tbody>
      </Table>
      {!!editingStore && (
        <EditBookStoreDrawer
          bookStore={editingStore}
          isOpen={isOpen}
          onClose={onClose}
        />
      )}
    </>
  );
}
