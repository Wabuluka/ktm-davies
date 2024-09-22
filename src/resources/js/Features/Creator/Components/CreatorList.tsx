import {
  Table,
  TableContainer,
  Tbody,
  Th,
  Thead,
  Tr,
  useDisclosure,
} from '@chakra-ui/react';
import { useState } from 'react';
import { Creator } from '../Types';
import { CreatorListItem } from './CreatorListItem';
import { EditCreatorDrawer } from './EditCreatorDrawer';

type Props = {
  creators: Creator[];
  selectable?: boolean | ((creator: Creator) => boolean);
  selectType?: 'none' | 'radio';
};

export function CreatorList({
  creators,
  selectable = true,
  selectType = 'none',
}: Props) {
  const { isOpen, onOpen, onClose } = useDisclosure();
  const [editingCreator, setEditingCreator] = useState<Creator>();

  function handleEdit(creator: Creator) {
    setEditingCreator(creator);
    onOpen();
  }

  return (
    <>
      <TableContainer>
        <Table>
          <Thead>
            <Tr>
              {selectable !== false && (
                <Th w={1} p={0} textAlign="center">
                  Select
                </Th>
              )}
              <Th>Name</Th>
              <Th w={1} textAlign="center">
                Operation
              </Th>
            </Tr>
          </Thead>
          <Tbody>
            {creators.map((creator) => {
              const isSelectable =
                typeof selectable === 'function'
                  ? selectable(creator)
                  : selectable;
              return (
                <CreatorListItem
                  key={creator.id}
                  creator={creator}
                  selectable={isSelectable}
                  selectType={selectType}
                  onEdit={handleEdit}
                />
              );
            })}
          </Tbody>
        </Table>
      </TableContainer>
      {!!editingCreator && (
        <EditCreatorDrawer
          isOpen={isOpen}
          onClose={onClose}
          creator={editingCreator}
        />
      )}
    </>
  );
}
