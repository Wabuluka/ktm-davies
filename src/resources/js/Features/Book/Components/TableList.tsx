import { TableListItem } from '@/Features/Book/Components/TableList/TableListItem';
import { Table, TableContainer, Tbody } from '@chakra-ui/react';
import React, { FC } from 'react';
import { SelectableBook } from '../Hooks/useSelectableBooks';
import { TableListHeader } from './TableList/TableListHeader';

type Props = {
  books: SelectableBook[];
  onSelect: (e: React.ChangeEvent<HTMLInputElement>) => void;
  onSelectAll: (e: React.ChangeEvent<HTMLInputElement>) => void;
};

export const TableList: FC<Props> = ({ books, onSelect, onSelectAll }) => {
  const isSelectedAll = books.every((book) => book.selected);

  return (
    <TableContainer whiteSpace="normal">
      <Table>
        <TableListHeader
          selectedAll={isSelectedAll}
          onSelectAll={onSelectAll}
        />
        <Tbody>
          {books.map((book) => (
            <TableListItem key={book.id} book={book} onSelect={onSelect} />
          ))}
        </Tbody>
      </Table>
    </TableContainer>
  );
};

export default TableList;
