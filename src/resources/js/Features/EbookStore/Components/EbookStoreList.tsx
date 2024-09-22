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
import { EbookStoreListItem } from '@/Features/EbookStore/Components/EbookStoreListItem';

type Props = {
  stores: BookStore[];
};

export function EbookStoreList({ stores }: Props) {
  return (
    <Box w="100%" overflowX="auto">
      <TableContainer>
        <Table>
          <Thead>
            <Tr>
              <Th>ID</Th>
              <Th>Store Name</Th>
              <Th>URL</Th>
              <Th>Purchase Option URL is Required</Th>
            </Tr>
          </Thead>
          <Tbody>
            {stores.map((store) => (
              <EbookStoreListItem key={store.id} store={store} />
            ))}
          </Tbody>
        </Table>
      </TableContainer>
    </Box>
  );
}
