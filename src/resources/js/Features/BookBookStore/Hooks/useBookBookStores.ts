import { useSetBookFormData } from '@/Features/Book/Context/BookFormContext';
import { useBookStores } from '@/Features/BookBookStore/Contexts/BookStoreDrawerContext';
import { useCallback } from 'react';

export const useBookBookStores = () => {
  const { setData } = useSetBookFormData();
  const { bookstores } = useBookStores();

  const updateBookStores = useCallback(() => {
    if (!bookstores) {
      return;
    } else setData((prev) => ({ ...prev, bookstores }));
  }, [bookstores, setData]);

  return {
    updateBookStores,
  };
};
