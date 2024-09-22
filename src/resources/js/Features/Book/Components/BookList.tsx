import { useMultiSelectInput } from '@/Hooks/Form/useMultiSelectInput';
import { useSelectInput } from '@/Hooks/Form/useSelectInput';
import {
  Checkbox,
  CheckboxGroup,
  RadioGroup,
  Table,
  Tbody,
  Text,
  Th,
  Thead,
  Tooltip,
  Tr,
} from '@chakra-ui/react';
import { useState } from 'react';
import { Book } from '../Types';
import { BookListItem } from './BookListItem';

type Props = {
  books: Book[];
  editable?: boolean;
} & (
  | {
      selectable?: false;
      multiSelect?: undefined;
      onSelectionChange?: undefined;
    }
  | {
      selectable: true | ((book: Book) => boolean);
      multiSelect: true;
      onSelectionChange: (books: Book[]) => void;
    }
  | {
      selectable: true | ((book: Book) => boolean);
      multiSelect?: false;
      onSelectionChange: (book: Book) => void;
    }
);

export function BookList({
  books,
  editable = true,
  selectable = false,
  multiSelect,
  onSelectionChange,
}: Props) {
  const [selectedId, setSelectedId] = useState<string>();
  const [selectedIds, setSelectedIds] = useState<string[]>([]);
  const checkboxProps = {
    value: selectedIds,
    ...useMultiSelectInput((ids) => {
      setSelectedIds(ids);
      const selectedBooks = books.filter((book) =>
        ids.some((id) => id === String(book.id)),
      );
      if (multiSelect) {
        onSelectionChange?.(selectedBooks);
      }
    }),
  };
  const radioProps = {
    value: selectedId,
    ...useSelectInput((id) => {
      setSelectedId(id);
      const book = books.find((book) => String(book.id) === id);
      if (book && !multiSelect) {
        onSelectionChange?.(book);
      }
    }),
  };
  const allSelected = books.every((book) =>
    selectedIds.includes(String(book.id)),
  );
  const selectType =
    selectable === false ? 'none' : multiSelect ? 'checkbox' : 'radio';

  const selectTh = (
    <Th w={1} p={0}>
      {multiSelect ? (
        <Checkbox
          size="lg"
          p={4}
          isChecked={allSelected}
          onChange={() => {
            allSelected
              ? setSelectedIds([])
              : setSelectedIds(books.map((b) => String(b.id)));
          }}
          aria-label={
            allSelected ? 'Deselect All of Books' : 'Select All of Books'
          }
        />
      ) : (
        <Text>Select</Text>
      )}
    </Th>
  );
  const table = (
    <Table>
      <Thead>
        <Tr>
          {selectable !== false && selectTh}
          <Th w={1} whiteSpace="nowrap">
            Image
          </Th>
          <Th w={1} whiteSpace="nowrap">
            Status
          </Th>
          <Th w={1} whiteSpace="nowrap" fontSize="lg">
            <Tooltip label="adult">🔞</Tooltip>
          </Th>
          <Th minW={{ base: 64, lg: 48, xl: 64 }} w="100%">
            Title
          </Th>
          <Th minW={48}>Publication Site</Th>
          <Th minW={48}>Label</Th>
          <Th w={1} whiteSpace="nowrap">
            Release Date
          </Th>
          <Th w={1} whiteSpace="nowrap">
            Last Updated
          </Th>
        </Tr>
      </Thead>
      <Tbody>
        {books.map((book) => (
          <BookListItem
            key={book.id}
            book={book}
            editable={editable}
            selectable={selectable}
            selectType={selectType}
          />
        ))}
      </Tbody>
    </Table>
  );

  switch (selectType) {
    case 'none':
      return table;
    case 'checkbox':
      return <CheckboxGroup {...checkboxProps}>{table}</CheckboxGroup>;
    case 'radio':
      return <RadioGroup {...radioProps}>{table}</RadioGroup>;
  }
}
