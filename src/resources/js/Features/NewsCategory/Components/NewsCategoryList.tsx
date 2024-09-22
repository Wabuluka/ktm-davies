import { NewsCategoryListItem } from '@/Features/NewsCategory/Components/NewsCategoryListItem';
import { NewsCategory } from '@/Features/NewsCategory/Types';
import { TableContainer, Table, Thead, Tr, Th, Tbody } from '@chakra-ui/react';

type Props = {
  categories: NewsCategory[];
};

export function NewsCategoryList({ categories }: Props) {
  return (
    <TableContainer>
      <Table>
        <Thead>
          <Tr>
            <Th>ID</Th>
            <Th>Category Name</Th>
          </Tr>
        </Thead>
        <Tbody>
          {categories.map((category) => (
            <NewsCategoryListItem key={category.id} category={category} />
          ))}
        </Tbody>
      </Table>
    </TableContainer>
  );
}
