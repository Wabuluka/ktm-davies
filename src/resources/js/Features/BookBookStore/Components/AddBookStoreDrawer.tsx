import { BookStoreForm } from '@/Features/BookBookStore/Components/BookStoreForm';
import { useBookStoresDispatch } from '@/Features/BookBookStore/Contexts/BookStoreDrawerContext';
import { BookStoreOnBookForm } from '@/Features/BookBookStore/Types';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { Drawer } from '@/UI/Components/Overlay/Drawer';
import { Button, ButtonGroup, Text, useId } from '@chakra-ui/react';

type Props = {
  isOpen: boolean;
  onClose: () => void;
};

export function AddBookStoreDrawer({ isOpen, onClose }: Props) {
  const formId = useId();
  const dispatch = useBookStoresDispatch();

  function handleSubmit(bookStore: BookStoreOnBookForm) {
    dispatch?.({ type: 'add', bookstore: bookStore });
    onClose();
  }

  return (
    <Drawer isOpen={isOpen} onClose={onClose}>
      <Text>Add Bookstore as Purchase Option</Text>
      <BookStoreForm id={formId} onSubmit={handleSubmit} />
      <ButtonGroup>
        <Button onClick={onClose}>Back</Button>
        <PrimaryButton type="submit" form={formId}>
          Save
        </PrimaryButton>
      </ButtonGroup>
    </Drawer>
  );
}
