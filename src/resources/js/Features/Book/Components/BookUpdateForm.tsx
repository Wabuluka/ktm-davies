import { useToast } from '@chakra-ui/react';
import { router } from '@inertiajs/react';
import {
  useBookFormState,
  useSetBookFormData,
  useSubmitBookFormData,
} from '../Context/BookFormContext';
import { BookForm } from './Form/BookForm';

type Props = {
  id: number;
};

export function BookUpdateForm({ id }: Props) {
  const { data, errors, processing, isDirty } = useBookFormState();
  const { updateBook } = useSubmitBookFormData();
  const { setData } = useSetBookFormData();
  const toast = useToast();

  function handleSubmit(
    e: React.FormEvent<HTMLFormElement> | React.FormEvent<HTMLInputElement>,
  ) {
    e.preventDefault();
    updateBook(id, {
      onSuccess: () =>
        toast({ title: `Edited ${data.title}`, status: 'success' }),
    });
  }

  function handleCopy() {
    if (isDirty) {
      if (!window.confirm('Changes will be discarded. Are you sure?')) {
        return;
      }
    }
    router.get(route('books.create', { from: id }));
  }

  return (
    <BookForm
      {...{ data, errors, setData, processing }}
      onSubmit={handleSubmit}
      onCopy={handleCopy}
    />
  );
}
