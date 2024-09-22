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
import { CreationType } from '../Types';
import { CreationTypeListItem } from './CreationTypeListItem';
import { EditCreationTypeDrawer } from './EditCreationTypeDrawer';

type Props = {
  creationTypes: CreationType[];
  selectType?: 'none' | 'radio';
};

export function CreationTypeList({
  creationTypes,
  selectType = 'none',
}: Props) {
  const { isOpen, onOpen, onClose } = useDisclosure();
  const [editingType, setEditingType] = useState<CreationType>();

  function handleEdit(creationType: CreationType) {
    setEditingType(creationType);
    onOpen();
  }

  return (
    <>
      <TableContainer>
        <Table>
          <Thead>
            <Tr>
              {selectType !== 'none' && (
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
            {creationTypes.map((creationType, i) => (
              <CreationTypeListItem
                key={creationType.name}
                creationType={creationType}
                selectType={selectType}
                firstItem={i === 0}
                lastItem={i === creationTypes.length - 1}
                onEdit={handleEdit}
              />
            ))}
          </Tbody>
        </Table>
      </TableContainer>
      {!!editingType && (
        <EditCreationTypeDrawer
          isOpen={isOpen}
          onClose={onClose}
          creationType={editingType}
        />
      )}
    </>
  );
}
