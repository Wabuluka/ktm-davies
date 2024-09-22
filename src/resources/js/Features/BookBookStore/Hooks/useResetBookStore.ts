import { useBookFormState } from '@/Features/Book/Context/BookFormContext';
import { useBookStoresDispatch } from '@/Features/BookBookStore/Contexts/BookStoreDrawerContext';
import { useCallback } from 'react';

export const useResetBookStores = () => {
  const {
    data: { bookstores: initialState },
  } = useBookFormState();
  const dispatch = useBookStoresDispatch();

  const resetBookStores = useCallback(() => {
    dispatch?.({ type: 'set', bookstores: initialState });
  }, [dispatch, initialState]);

  return {
    resetBookStores,
  };
};
