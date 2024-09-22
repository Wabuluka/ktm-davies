import { Checkbox, Th, Thead, Tooltip, Tr } from '@chakra-ui/react';
import React from 'react';

type Props = {
  selectedAll: boolean;
  onSelectAll: (e: React.ChangeEvent<HTMLInputElement>) => void;
};

export function TableListHeader({ selectedAll, onSelectAll }: Props) {
  return (
    <Thead>
      <Tr>
        <Th w={1} p={0}>
          <Checkbox
            size="lg"
            p={4}
            isChecked={selectedAll}
            onChange={onSelectAll}
            aria-label={selectedAll ? 'Deselect all books' : 'Select all books'}
          />
        </Th>
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
  );
}
