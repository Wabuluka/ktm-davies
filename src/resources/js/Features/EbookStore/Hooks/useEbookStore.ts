import { useEbookStores } from '@/Features/EbookStore/Hooks/useEbookStores';
import { useMemo } from 'react';

export function useEbookStore(ebookstoreId: string | number) {
  const ebookstores = useEbookStores();

  return useMemo(
    () => ebookstores.find((ebookstore) => ebookstore.id == ebookstoreId),
    [ebookstores, ebookstoreId],
  );
}
