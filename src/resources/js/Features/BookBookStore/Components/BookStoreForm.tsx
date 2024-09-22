import { useBookStores } from '@/Features/BookBookStore/Contexts/BookStoreDrawerContext';
import { BookStoreOnBookForm } from '@/Features/BookBookStore/Types';
import { BookStoreSelect, useBookStore } from '@/Features/BookStore';
import {
  FormControl,
  FormHelperText,
  FormLabel,
  Input,
  VStack,
} from '@chakra-ui/react';
import { ComponentProps, useState } from 'react';

type Props = {
  initialValues?: BookStoreOnBookForm;
  onSubmit: (bookStore: BookStoreOnBookForm) => void;
} & Omit<ComponentProps<'form'>, 'onSubmit'>;

export function BookStoreForm({
  initialValues = {
    id: '',
    url: '',
    is_primary: false,
  },
  onSubmit,
  ...props
}: Props) {
  const { selectedStoreIds } = useBookStores();
  const [formData, setFormData] = useState<BookStoreOnBookForm>(initialValues);
  const isPurchaseUrlRequired =
    useBookStore(formData.id)?.is_purchase_url_required ?? false;

  function onChangeId(e: React.ChangeEvent<HTMLSelectElement>) {
    setFormData((formData) => ({ ...formData, id: e.target.value }));
  }
  function onChangeUrl(e: React.ChangeEvent<HTMLInputElement>) {
    setFormData((formData) => ({ ...formData, url: e.target.value }));
  }
  function handleSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    e.stopPropagation();
    onSubmit(formData);
  }

  return (
    <form onSubmit={handleSubmit} {...props}>
      <VStack spacing={4}>
        <FormControl isRequired>
          <FormLabel>Bookstore</FormLabel>
          <BookStoreSelect
            value={formData.id}
            disabledStoreIds={selectedStoreIds}
            onChange={onChangeId}
          />
        </FormControl>
        <FormControl isRequired={isPurchaseUrlRequired}>
          <FormLabel>Bookstore URL</FormLabel>
          <Input
            type="url"
            pattern="^https?://.+\..+"
            autoComplete="url"
            value={formData.url}
            onChange={onChangeUrl}
            maxLength={2048}
            placeholder="https://example.com/"
          />
          <FormHelperText lineHeight={1.6}>
            Please enter a URL that starts with http(s).
            <br />
            {!isPurchaseUrlRequired &&
              "If left blank, it will be automatically generated based on the book's ISBN"}
          </FormHelperText>
        </FormControl>
      </VStack>
    </form>
  );
}
