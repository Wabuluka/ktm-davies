import { useEbookStores } from '@/Features/EbookStore/Hooks/useEbookStores';
import { Select, SelectProps } from '@chakra-ui/react';

type Props = SelectProps & {
  disabledStoreIds?: number[];
};

export function EbookStoreSelect({ disabledStoreIds, ...props }: Props) {
  const ebookstores = useEbookStores();

  return (
    <Select {...props}>
      <option value="">Please select</option>
      {ebookstores.map((ebookStore) => (
        <option
          key={ebookStore.id}
          value={ebookStore.id}
          disabled={disabledStoreIds?.includes(ebookStore.id)}
        >
          {ebookStore.store.name}
        </option>
      ))}
    </Select>
  );
}
