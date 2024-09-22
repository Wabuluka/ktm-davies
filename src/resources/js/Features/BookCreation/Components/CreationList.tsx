import { List, ListProps, useDisclosure } from '@chakra-ui/react';
import { useState } from 'react';
import { CreationOnBookForm } from '../Types';
import { CreationListItem } from './CreationListItem';
import { EditCreationDrawer } from './EditCreationDrawer';

type Props = {
  creations: CreationOnBookForm[];
} & Omit<ListProps, 'children'>;

export function CreationList({ creations, ...props }: Props) {
  const [editingCreation, setEditingCreation] = useState<CreationOnBookForm>();
  const { isOpen, onOpen, onClose } = useDisclosure();
  function handleEditButtonClick(creation: CreationOnBookForm) {
    setEditingCreation(creation);
    onOpen();
  }

  return (
    <List display="flex" flexDir="column" gap={4} p={4} {...props}>
      {creations.map((creation, i) => (
        <CreationListItem
          key={creation.creator_id}
          firstItem={i === 0}
          lastItem={i === creations.length - 1}
          creation={creation}
          onEdit={handleEditButtonClick}
        />
      ))}
      {editingCreation && (
        <EditCreationDrawer
          creation={editingCreation}
          isOpen={isOpen}
          onClose={onClose}
        />
      )}
    </List>
  );
}
