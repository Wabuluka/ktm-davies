import { NewsListItem } from '@/Features/News/Components/NewsListItem';
import { News } from '@/Features/News/Types';
import { Table, TableContainer, Tbody, Th, Thead, Tr } from '@chakra-ui/react';

type Props = {
  newsList: News[];
};

export function NewsList({ newsList }: Props) {
  return (
    <TableContainer>
      <Table>
        <Thead>
          <Tr>
            <Th>ID</Th>
            <Th>Status</Th>
            <Th>Category</Th>
            <Th>Title</Th>
            <Th>Release Date</Th>
          </Tr>
        </Thead>
        <Tbody>
          {newsList.map((news) => (
            <NewsListItem key={news.id} news={news} />
          ))}
        </Tbody>
      </Table>
    </TableContainer>
  );
}
