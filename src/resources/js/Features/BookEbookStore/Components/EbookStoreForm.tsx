import { EbookStoreOnBookForm } from '@/Features/BookEbookStore/Types';
import { EbookStoreSelect } from '@/Features/EbookStore/Components/EbookStoreSelect';
import { useEbookStore } from '@/Features/EbookStore/Hooks/useEbookStore';
import {
  FormControl,
  FormHelperText,
  FormLabel,
  Input,
  VStack,
} from '@chakra-ui/react';
import { ComponentProps, useState } from 'react';
import { useEbookStores } from '../Contexts/EbookStoreDrawerContext';

type Props = {
  initialValues?: EbookStoreOnBookForm;
  onSubmit: (ebookstore: EbookStoreOnBookForm) => void;
} & Omit<ComponentProps<'form'>, 'onSubmit'>;

export function EbookStoreForm({
  initialValues = {
    id: '',
    url: '',
    is_primary: false,
  },
  onSubmit,
  ...props
}: Props) {
  const { selectedStoreIds } = useEbookStores();
  const [formData, setFormData] = useState<EbookStoreOnBookForm>(initialValues);
  const isPurchaseUrlRequired =
    useEbookStore(formData.id)?.is_purchase_url_required ?? false;

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
          <FormLabel>Purchase Option</FormLabel>
          <EbookStoreSelect
            value={formData.id}
            disabledStoreIds={selectedStoreIds}
            onChange={onChangeId}
          />
        </FormControl>
        <FormControl isRequired={isPurchaseUrlRequired}>
          <FormLabel>Purchase URL</FormLabel>
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
            Enter URL that starts with http(s).
            <br />
            {!isPurchaseUrlRequired &&
              "If left blank, it will be automatically generated based on the book's information something like title"}
          </FormHelperText>
        </FormControl>
      </VStack>
    </form>
  );
}
