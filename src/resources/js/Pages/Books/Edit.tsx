import { BookUpdateForm } from '@/Features/Book';
import { BookFormProvider } from '@/Features/Book/Context/BookFormContext';
import { EditingBookProvider } from '@/Features/Book/Context/EditingBookContext';
import { AuthenticatedLayout } from '@/Layouts/Authenticated';
import { VStack } from '@chakra-ui/react';
import { Book } from '../../Features/Book/Types/index';
import { createBookFormInitialValues } from '@/Features/Book/Utils/createBookFormInitialValues';

type Props = {
  book: Book;
};

export default function Edit({ book }: Props) {
  const initialValues = createBookFormInitialValues({ type: 'edit', book });

  return (
    <AuthenticatedLayout title="Edit a Book" pageCategory="Book">
      <VStack align="stretch" spacing={8}>
        <EditingBookProvider book={book}>
          <BookFormProvider initialValues={initialValues}>
            <BookUpdateForm id={book.id} />
          </BookFormProvider>
        </EditingBookProvider>
      </VStack>
    </AuthenticatedLayout>
  );
}
