import { SearchFormBase } from '@/UI/Components/Form/SearchFormBase';
import { SearchInput } from '@/UI/Components/Form/SearchInput';
import { FormControl, FormLabel } from '@chakra-ui/react';
import { ComponentProps, useState } from 'react';
import { IndexCreatorQueryParams } from '../Hooks/useIndexCreatorQuery';

export type CreatorSearchFormParams = Omit<IndexCreatorQueryParams, 'page'>;

type Props = {
  onSubmit: (params: CreatorSearchFormParams) => void;
  initialValues?: CreatorSearchFormParams;
} & Omit<ComponentProps<'form'>, 'onSubmit'>;

export function CreatorSearchForm({
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
