import { SearchFormBase } from '@/UI/Components/Form/SearchFormBase';
import { SearchInput } from '@/UI/Components/Form/SearchInput';
import { FormControl, FormLabel } from '@chakra-ui/react';
import { ComponentProps, useState } from 'react';

export type SearchParameters = {
  keyword: string;
};

type Props = {
  onSubmit: (params: SearchParameters) => void;
  initialValues?: SearchParameters;
} & Omit<ComponentProps<'form'>, 'onSubmit'>;

export function ExternalLinkSearchForm({
  onSubmit,
  initialValues = { keyword: '' },
  ...props
}: Props) {
  const [params, setParams] = useState(initialValues);
  function handleKeywordChange(e: React.ChangeEvent<HTMLInputElement>) {
    setParams((prev) => ({ ...prev, keyword: e.target.value }));
  }
  function handleSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    e.stopPropagation();
    onSubmit(params);
  }

  return (
    <SearchFormBase onSubmit={handleSubmit} {...props}>
      <FormControl>
        <FormLabel>Keywords</FormLabel>
        <SearchInput value={params.keyword} onChange={handleKeywordChange} />
      </FormControl>
    </SearchFormBase>
  );
}
