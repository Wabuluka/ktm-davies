import { useBookStores } from '@/Features/BookStore/Hooks/useBookStores';
import { Select, SelectProps } from '@chakra-ui/react';

type Props = SelectProps & {
  disabledStoreIds?: number[];
};

export function BookStoreSelect({ disabledStoreIds, ...props }: Props) {
  const bookStores = useBookStores();

  return (
    <Select {...props}>
      <option value="">Please select</option>
      {bookStores.map((bookStore) => (
        <option
          key={bookStore.id}
          value={bookStore.id}
          disabled={disabledStoreIds?.includes(bookStore.id)}
        >
          {bookStore.store.name}
        </option>
      ))}
    </Select>
  );
}
