import { BookStoreForm } from '@/Features/BookBookStore/Components/BookStoreForm';
import { useBookStoresDispatch } from '@/Features/BookBookStore/Contexts/BookStoreDrawerContext';
import { BookStoreOnBookForm } from '@/Features/BookBookStore/Types';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { Drawer } from '@/UI/Components/Overlay/Drawer';
import { Button, ButtonGroup, Text } from '@chakra-ui/react';
import { useId } from 'react';

type Props = {
  bookStore: BookStoreOnBookForm;
  isOpen: boolean;
  onClose: () => void;
};

export function EditBookStoreDrawer({
  bookStore: initialValues,
  isOpen,
  onClose,
}: Props) {
  const formId = useId();
  const dispatch = useBookStoresDispatch();

  function handleSubmit(bookStore: BookStoreOnBookForm) {
    dispatch?.({
      type: 'update',
      bookstore: bookStore,
      id: initialValues.id,
    });
    onClose();
  }

  return (
    <Drawer isOpen={isOpen} onClose={onClose}>
      <Text>Edit Purchase Option (Physical Books)</Text>
      <BookStoreForm
        id={formId}
        initialValues={initialValues}
        onSubmit={handleSubmit}
      />
      <ButtonGroup>
        <Button onClick={onClose}>Back</Button>
        <PrimaryButton type="submit" form={formId}>
          Save
        </PrimaryButton>
      </ButtonGroup>
    </Drawer>
  );
}
