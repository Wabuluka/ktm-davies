import { BookCreateForm } from '@/Features/Book';
import { BookFormProvider } from '@/Features/Book/Context/BookFormContext';
import { Book } from '@/Features/Book/Types';
import { createBookFormInitialValues } from '@/Features/Book/Utils/createBookFormInitialValues';
import { AuthenticatedLayout } from '@/Layouts/Authenticated';
import { VStack } from '@chakra-ui/react';

type Props = {
  book: Book | null;
};

export default function Create({ book }: Props) {
  const initialValues = book
    ? createBookFormInitialValues({ type: 'copy', book })
    : createBookFormInitialValues({ type: 'create' });

  return (
    <AuthenticatedLayout title="Create a book" pageCategory="Book">
      <VStack align="stretch" spacing={8}>
        <BookFormProvider initialValues={initialValues}>
          <BookCreateForm />
        </BookFormProvider>
      </VStack>
    </AuthenticatedLayout>
  );
}
