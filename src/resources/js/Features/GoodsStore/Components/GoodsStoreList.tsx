import {
  Box,
  Table,
  TableContainer,
  Tbody,
  Th,
  Thead,
  Tr,
} from '@chakra-ui/react';
import { GoodsStore } from '@/Features/GoodsStore/Types';
import { GoodsStoreListItem } from '@/Features/GoodsStore/Components/GoodsStoreListItem';

type Props = {
  stores: GoodsStore[];
};

export function GoodsStoreList({ stores }: Props) {
  return (
    <Box w="100%" overflowX="auto">
      <TableContainer>
        <Table>
          <Thead>
            <Tr>
              <Th>ID</Th>
              <Th>Store Name</Th>
              <Th>URL</Th>
            </Tr>
          </Thead>
          <Tbody>
            {stores.map((store) => (
              <GoodsStoreListItem key={store.id} store={store} />
            ))}
          </Tbody>
        </Table>
      </TableContainer>
    </Box>
  );
}
