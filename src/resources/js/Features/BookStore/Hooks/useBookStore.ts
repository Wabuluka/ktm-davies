import { useMemo } from 'react';
import { useBookStores } from './useBookStores';

export function useBookStore(bookStoreId: string | number) {
  const bookStores = useBookStores();

  return useMemo(
    () => bookStores.find((store) => store.id == bookStoreId),
    [bookStores, bookStoreId],
  );
}
