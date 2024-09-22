import { Paginator } from '@/Api/Types';
import { BulkActions } from '@/Features/Book';
import { BookList } from '@/Features/Book/Components/BookList';
import { useBookSearchInertiaForm } from '@/Features/Book/Hooks/useBookSearchInertiaForm';
import { Book } from '@/Features/Book/Types';
import { AuthenticatedLayout } from '@/Layouts/Authenticated';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { Link } from '@/UI/Components/Navigation/Link';
import { PaginatorBase } from '@/UI/Components/Navigation/PaginatorBase';
import { Heading } from '@/UI/Components/Typography/Heading';
import { Box, Fade, Flex, LinkBox, Spacer, VStack } from '@chakra-ui/react';
import { useState } from 'react';
import { BsBook, BsPlus, BsSearch } from 'react-icons/bs';

type Props = {
  books: Paginator<Book>;
};

export default function Index({ books }: Props) {
  const [selectedBooks, setSelectedBooks] = useState<Book[]>([]);
  const shouldShowBulkActions = selectedBooks.length > 0;
  const { searchForm, onPageChange } = useBookSearchInertiaForm({
    onSubmit: () => setSelectedBooks([]),
  });

  function handleSelectionChange(books: Book[]) {
    setSelectedBooks(books);
  }
  function handlePageChange(page: number) {
    onPageChange(page);
    setSelectedBooks([]);
  }
  function handleBulkActionSuccess() {
    setSelectedBooks([]);
  }

  return (
    <AuthenticatedLayout title="Book information" pageCategory="Book">
      <VStack spacing={12} align="stretch">
        <Heading as="h2" icon={<BsSearch />}>
          Book Search
        </Heading>
        {searchForm}
        <Box as="section" w="100%">
          <Heading as="h2" icon={<BsBook />} mb={8}>
            Book List
          </Heading>
          <Flex
            as="section"
            direction={{ base: 'column-reverse', lg: 'row' }}
            justify="space-around"
            gap={4}
            w="100%"
          >
            <Fade in={shouldShowBulkActions}>
              <BulkActions
                books={selectedBooks}
                onPublishSuccess={handleBulkActionSuccess}
                onDeleteSuccess={handleBulkActionSuccess}
              />
            </Fade>
            <Spacer />
            <PrimaryButton as={LinkBox} leftIcon={<BsPlus />}>
              <Link href={route('books.create')} overlay>
                New
              </Link>
            </PrimaryButton>
          </Flex>
          <BookList
            key={JSON.stringify(books.data)}
            books={books.data}
            editable={true}
            selectable={true}
            multiSelect={true}
            onSelectionChange={handleSelectionChange}
          />
          <PaginatorBase
            onPageChange={handlePageChange}
            lastPage={books.meta.last_page}
            currentIndex={books.meta.current_page}
          />
        </Box>
      </VStack>
    </AuthenticatedLayout>
  );
}
