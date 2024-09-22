import { Table, TableContainer, Tbody, Th, Thead, Tr } from '@chakra-ui/react';
import { RelatedItemOnBookForm } from '../Types';
import { RelatedItemListItem } from './RelatedItemListItem';

type Props = {
  relatedItems: RelatedItemOnBookForm[];
  onItemOrderUp: (relatedItem: RelatedItemOnBookForm) => void;
  onItemOrderDown: (relatedItem: RelatedItemOnBookForm) => void;
  onItemEdit: (relatedItem: RelatedItemOnBookForm) => void;
  onItemDelete: (relatedItem: RelatedItemOnBookForm) => void;
};

export function RelatedItemList({
  relatedItems,
  onItemOrderUp,
  onItemOrderDown,
  onItemEdit,
  onItemDelete,
}: Props) {
  return (
    <TableContainer>
      <Table>
        <Thead>
          <Tr>
            <Th w={1} whiteSpace="nowrap">
              Type
            </Th>
            <Th>Description</Th>
            <Th w={1} whiteSpace="nowrap">
              Thumbnail
            </Th>
            <Th w={1}>Operation</Th>
          </Tr>
        </Thead>
        <Tbody>
          {relatedItems.map((relateditem, i) => (
            <RelatedItemListItem
              key={relateditem.id}
              relatedItem={relateditem}
              firstItem={i === 0}
              lastItem={i === relatedItems.length - 1}
              onOrderUp={onItemOrderUp}
              onOrderDown={onItemOrderDown}
              onEdit={onItemEdit}
              onDelete={onItemDelete}
            />
          ))}
        </Tbody>
      </Table>
    </TableContainer>
  );
}
