import {
  useBookStores,
  useBookStoresDispatch,
} from '@/Features/BookBookStore/Contexts/BookStoreDrawerContext';
import { useCallback } from 'react';

export const usePrimaryBookStore = () => {
  const { primaryStore } = useBookStores();
  const dispatch = useBookStoresDispatch();
  const handlePrimaryStoreChange = useCallback(
    (id: string) => {
      dispatch?.({ type: 'update-primary', id });
    },
    [dispatch],
  );
  const handlePrimaryStoreUnselect = useCallback(() => {
    dispatch?.({ type: 'unset-primary' });
  }, [dispatch]);

  return {
    primaryStore,
    handlePrimaryStoreChange,
    handlePrimaryStoreUnselect,
  };
};
