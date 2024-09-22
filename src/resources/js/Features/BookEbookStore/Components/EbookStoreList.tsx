import { Table, Tbody, Th, Thead, Tr, useDisclosure } from '@chakra-ui/react';
import { useState } from 'react';
import {
  useEbookStores,
  useEbookStoresDispatch,
} from '../Contexts/EbookStoreDrawerContext';
import { EbookStoreOnBookForm } from '../Types';
import { EbookStoreListItem } from './EbookStoreListItem';
import { EditEbookStoreDrawer } from './EditEbookStoreDrawer';

type Props = {
  selectType?: 'radio' | 'none';
};

export function EbookStoreList({ selectType = 'none' }: Props) {
  const { isOpen, onClose, onOpen } = useDisclosure();
  const [editingStore, setEditingStore] = useState<EbookStoreOnBookForm>();
  const { ebookstores } = useEbookStores();
  const dispatch = useEbookStoresDispatch();

  function handleEditButtonClick(ebookstore: EbookStoreOnBookForm) {
    setEditingStore(ebookstore);
    onOpen();
  }
  function handleDeleteButtonClick({ id }: EbookStoreOnBookForm) {
    dispatch?.({ type: 'delete', id });
  }

  return (
    <>
      <Table>
        <Thead>
          <Tr>
            {selectType !== 'none' && (
              <Th w={1} whiteSpace="nowrap">
                Show in list
              </Th>
            )}
            <Th>Purchase Option</Th>
            <Th>URL</Th>
            <Th w={1}>Operation</Th>
          </Tr>
        </Thead>
        <Tbody>
          {ebookstores?.map((ebookstore) => (
            <EbookStoreListItem
              key={ebookstore.id}
              ebookstore={ebookstore}
              selectType={selectType}
              onEdit={handleEditButtonClick}
              onDelete={handleDeleteButtonClick}
            />
          ))}
        </Tbody>
      </Table>
      {!!editingStore && (
        <EditEbookStoreDrawer
          ebookstore={editingStore}
          isOpen={isOpen}
          onClose={onClose}
        />
      )}
    </>
  );
}
