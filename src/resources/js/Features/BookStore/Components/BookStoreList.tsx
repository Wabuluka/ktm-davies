import {
  Box,
  Table,
  TableContainer,
  Tbody,
  Th,
  Thead,
  Tr,
} from '@chakra-ui/react';
import { BookStore } from '@/Features/BookStore/Types';
import { BookStoreListItem } from '@/Features/BookStore/Components/BookStoreListItem';

type Props = {
  stores: BookStore[];
};

export function BookStoreList({ stores }: Props) {
  return (
    <Box w="100%" overflowX="auto">
      <TableContainer>
        <Table>
          <Thead>
            <Tr>
              <Th>ID</Th>
              <Th>Store Name</Th>
              <Th>URL</Th>
              <Th>購入先URLの登録が必須 Purchase Option URL is required</Th>
            </Tr>
          </Thead>
          <Tbody>
            {stores.map((store) => (
              <BookStoreListItem key={store.id} store={store} />
            ))}
          </Tbody>
        </Table>
      </TableContainer>
    </Box>
  );
}
