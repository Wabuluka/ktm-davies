import { AddBookStoreDrawer } from '@/Features/BookBookStore/Components/AddBookStoreDrawer';
import { BookStoreList } from '@/Features/BookBookStore/Components/BookStoreList';
import { BookStoreDrawerProvider } from '@/Features/BookBookStore/Contexts/BookStoreDrawerContext';
import { useBookBookStores } from '@/Features/BookBookStore/Hooks/useBookBookStores';
import { usePrimaryBookStore } from '@/Features/BookBookStore/Hooks/usePrimaryBookStore';
import { useResetBookStores } from '@/Features/BookBookStore/Hooks/useResetBookStore';
import { BookStoreOnBookForm } from '@/Features/BookBookStore/Types';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { Drawer } from '@/UI/Components/Overlay/Drawer';
import { Heading } from '@/UI/Components/Typography/Heading';
import {
  Button,
  ButtonGroup,
  RadioGroup,
  Text,
  VStack,
  useDisclosure,
} from '@chakra-ui/react';
import { useId } from 'react';

type Props = {
  bookStores: BookStoreOnBookForm[];
  isOpen: boolean;
  onClose: () => void;
};

export function BookStoreDrawerBase({
  isOpen,
  onClose,
}: Omit<Props, 'bookStores'>) {
  const formId = useId();
  const addDrawerDisclosure = useDisclosure();
  const { updateBookStores } = useBookBookStores();
  const { primaryStore, handlePrimaryStoreChange, handlePrimaryStoreUnselect } =
    usePrimaryBookStore();
  const { resetBookStores } = useResetBookStores();

  function handleClose() {
    resetBookStores();
    onClose();
  }
  function handleSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    e.stopPropagation();
    updateBookStores();
    onClose();
  }

  return (
    <>
      <Drawer isOpen={isOpen} onClose={handleClose}>
        <Text>Information of Purchase Option (Physical Book)</Text>
        <form id={formId} onSubmit={handleSubmit}>
          <VStack align="stretch" spacing={8}>
            <Heading>List</Heading>
            <RadioGroup
              value={primaryStore?.id || ''}
              onChange={handlePrimaryStoreChange}
            >
              <BookStoreList selectType="radio" />
            </RadioGroup>
            <ButtonGroup>
              <PrimaryButton onClick={addDrawerDisclosure.onOpen}>
                Add a Bookstore as Purchase Option
              </PrimaryButton>
              {!!primaryStore && (
                <Button onClick={handlePrimaryStoreUnselect}>
                  Don't show Purchase Option in List
                </Button>
              )}
            </ButtonGroup>
          </VStack>
        </form>
        <ButtonGroup>
          <Button onClick={handleClose}>Back</Button>
          <PrimaryButton type="submit" form={formId}>
            Save
          </PrimaryButton>
        </ButtonGroup>
      </Drawer>
      <AddBookStoreDrawer
        isOpen={addDrawerDisclosure.isOpen}
        onClose={addDrawerDisclosure.onClose}
      />
    </>
  );
}

export function BookStoreDrawer({ bookStores, isOpen, onClose }: Props) {
  return (
    <BookStoreDrawerProvider initialState={bookStores}>
      <BookStoreDrawerBase isOpen={isOpen} onClose={onClose} />
    </BookStoreDrawerProvider>
  );
}
