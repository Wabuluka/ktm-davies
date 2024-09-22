import {
  useBookFormState,
  useSetBookFormData,
} from '@/Features/Book/Context/BookFormContext';
import { BookFormData } from '@/Features/Book/Types';
import { RelatedItemList } from '@/Features/RelatedItem';
import { useEditRelatedItemDrawer } from '@/Features/RelatedItem/Hooks/useEditRelatedItemDrawer';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { Drawer } from '@/UI/Components/Overlay/Drawer';
import { Heading } from '@/UI/Components/Typography/Heading';
import { Box, Button, ButtonGroup, Text, VStack } from '@chakra-ui/react';
import { useState } from 'react';
import {
  RelatedItemsProvider,
  useDispatchRelatedItems,
  useRelatedItems,
} from '../Contexts/RelatedItemsContext';
import { useAddRelatedItemDrawer } from '../Hooks/useAddRelatedItemDrawer';
import { RelatedItemFormData, RelatedItemOnBookForm } from '../Types';

type Props = {
  relatedItems: BookFormData['related_items'];
  isOpen: boolean;
  onClose: () => void;
};

function RelatedItemDrawerBase({
  isOpen,
  onClose,
}: Omit<Props, 'relatedItems'>) {
  const {
    data: { related_items: initialState },
  } = useBookFormState();
  const { setData } = useSetBookFormData();
  const relatedItems = useRelatedItems();
  const dispatch = useDispatchRelatedItems();
  const [editingItem, setEditingItem] = useState<RelatedItemOnBookForm>();
  const { onOpen: onOpenEditDrawer, editRelatedItemDrawer } =
    useEditRelatedItemDrawer({
      relatedItem: editingItem,
      onSubmit: (relatedItem: RelatedItemFormData) => {
        if (editingItem) {
          dispatch?.({ type: 'update', relatedItem, id: editingItem.id });
        }
      },
    });
  const { onOpen: onOpenAddDrawer, addRelatedItemDrawer } =
    useAddRelatedItemDrawer({
      onSubmit: (relatedItem: RelatedItemFormData) =>
        dispatch?.({ type: 'add', relatedItem }),
    });
  function handleItemOrderUp(relatedItem: RelatedItemOnBookForm) {
    dispatch?.({ type: 'move-up', relatedItem });
  }
  function handleItemOrderDown(relatedItem: RelatedItemOnBookForm) {
    dispatch?.({ type: 'move-down', relatedItem });
  }
  function handleItemEdit(relatedItem: RelatedItemOnBookForm) {
    setEditingItem(relatedItem);
    onOpenEditDrawer();
  }
  function handleItemDelete(relatedItem: RelatedItemOnBookForm) {
    dispatch?.({ type: 'delete', id: relatedItem.id });
  }
  function handleClose() {
    if (dispatch && relatedItems) {
      dispatch?.({ type: 'set', newState: initialState });
    }
    onClose();
  }
  function handleSubmit() {
    if (relatedItems) {
      setData('related_items', relatedItems);
    }
    onClose();
  }

  if (!relatedItems) {
    return null;
  }

  return (
    <Drawer isOpen={isOpen} onClose={handleClose}>
      <Text>Edit a Related Items</Text>
      <>
        <VStack align="stretch" spacing={8}>
          <Heading as="div">List</Heading>
          <RelatedItemList
            relatedItems={relatedItems.upsert}
            onItemOrderUp={handleItemOrderUp}
            onItemOrderDown={handleItemOrderDown}
            onItemEdit={handleItemEdit}
            onItemDelete={handleItemDelete}
          />
          <Box>
            <PrimaryButton onClick={onOpenAddDrawer}>Add</PrimaryButton>
          </Box>
        </VStack>
        {addRelatedItemDrawer}
        {editRelatedItemDrawer}
      </>
      <ButtonGroup>
        <Button onClick={handleClose}>Back</Button>
        <PrimaryButton onClick={handleSubmit}>Save</PrimaryButton>
      </ButtonGroup>
    </Drawer>
  );
}

export function RelatedItemDrawer({ relatedItems, isOpen, onClose }: Props) {
  return (
    <RelatedItemsProvider initialState={relatedItems}>
      <RelatedItemDrawerBase isOpen={isOpen} onClose={onClose} />
    </RelatedItemsProvider>
  );
}
