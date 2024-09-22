import { DataFetchError } from '@/UI/Components/Feedback/DataFetchError';
import { LoadingSpinner } from '@/UI/Components/Feedback/LoadingSpinner';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { PaginatorBase } from '@/UI/Components/Navigation/PaginatorBase';
import { Drawer } from '@/UI/Components/Overlay/Drawer';
import { Button, ButtonGroup, Text, VStack } from '@chakra-ui/react';
import { useId, useState } from 'react';
import { useRelatedItems } from '../../RelatedItem/Contexts/RelatedItemsContext';
import { useEditingBook } from '../Context/EditingBookContext';
import { useBookSearchApiForm } from '../Hooks/useBookSearchApiForm';
import { Book } from '../Types';
import { BookList } from './BookList';

type Props = {
  isOpen: boolean;
  onClose: () => void;
  onSubmit: (book: Book) => void;
};

export function SelectBookDrawer({ isOpen, onClose, onSubmit }: Props) {
  const formId = useId();
  const editingBook = useEditingBook();
  const [selectedBook, setSelectedBook] = useState<Book>();
  const relatedItems = useRelatedItems();
  const addedBooks = relatedItems?.upsert.filter(
    (item) => item.relatable_type === 'book',
  );
  const {
    data: paginator,
    isLoading,
    isError,
    searchForm,
    onPageChange,
  } = useBookSearchApiForm({
    onResultUpdate: () => setSelectedBook(undefined),
  });
  function selectable(book: Book) {
    if (editingBook?.id === book.id) {
      return false;
    }
    if (!addedBooks) {
      return true;
    }
    return addedBooks.every((item) => item.relatable_id !== String(book.id));
  }
  function handleSelectionChange(book: Book) {
    setSelectedBook(book);
  }
  function handleClose() {
    setSelectedBook(undefined);
    onClose();
  }
  function handleSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    e.stopPropagation();
    selectedBook && onSubmit(selectedBook);
    handleClose();
  }

  return (
    <Drawer isOpen={isOpen} onClose={onClose}>
      <Text>Select from internal works</Text>
      <VStack align="stretch" spacing={8}>
        {searchForm}
        {isLoading ? (
          <LoadingSpinner />
        ) : isError || !paginator ? (
          <DataFetchError />
        ) : (
          <>
            <form id={formId} onSubmit={handleSubmit}>
              <BookList
                books={paginator.data}
                editable={false}
                selectable={selectable}
                onSelectionChange={handleSelectionChange}
              />
            </form>
            <PaginatorBase
              onPageChange={onPageChange}
              lastPage={paginator.meta.last_page}
              currentIndex={paginator.meta.current_page}
            />
          </>
        )}
      </VStack>
      <ButtonGroup>
        <Button onClick={handleClose}>Back</Button>
        <PrimaryButton form={formId} isDisabled={!selectedBook} type="submit">
          Select
        </PrimaryButton>
      </ButtonGroup>
    </Drawer>
  );
}
