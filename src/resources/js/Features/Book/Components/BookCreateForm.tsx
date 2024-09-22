import { useToast } from '@chakra-ui/react';
import {
  useBookFormState,
  useSetBookFormData,
  useSubmitBookFormData,
} from '../Context/BookFormContext';
import { BookForm } from './Form/BookForm';

export function BookCreateForm() {
  const { data, errors, processing } = useBookFormState();
  const { storeBook } = useSubmitBookFormData();
  const { setData } = useSetBookFormData();
  const toast = useToast();

  function handleSubmit(
    e: React.FormEvent<HTMLFormElement> | React.FormEvent<HTMLInputElement>,
  ) {
    e.preventDefault();
    storeBook({
      onSuccess: () =>
        toast({ title: `Created ${data.title}`, status: 'success' }),
    });
  }

  return (
    <BookForm
      {...{ data, errors, setData, processing }}
      onSubmit={handleSubmit}
    />
  );
}
