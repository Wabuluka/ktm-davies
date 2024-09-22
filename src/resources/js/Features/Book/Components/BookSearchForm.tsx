import { SiteCheckBox } from '@/Features/Site/Components/SiteCheckbox';
import { useSites } from '@/Features/Site/Hooks/useSites';
import { useMultiSelectInput } from '@/Hooks/Form/useMultiSelectInput';
import { useTextInput } from '@/Hooks/Form/useTextInput';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { SearchFormBase } from '@/UI/Components/Form/SearchFormBase';
import { SearchInput } from '@/UI/Components/Form/SearchInput';
import { Search2Icon } from '@chakra-ui/icons';
import {
  Accordion,
  AccordionButton,
  AccordionIcon,
  AccordionItem,
  AccordionPanel,
  Checkbox,
  CheckboxGroup,
  Flex,
  FormControl,
  FormHelperText,
  FormLabel,
  HStack,
  Text,
  VStack,
} from '@chakra-ui/react';
import { ComponentProps, useState } from 'react';
import { BookStatus } from '../Types';
import { StatusBadge } from './StatusBadge';

export type SearchParameters = {
  keyword: string;
  statuses: BookStatus[];
  sites: string[];
};

type Props = {
  onSubmit: (params: SearchParameters) => void;
  initialValues?: SearchParameters;
} & Omit<ComponentProps<'form'>, 'onSubmit'>;

export function BookSearchForm({
  onSubmit,
  initialValues = { keyword: '', statuses: [], sites: [] },
  ...props
}: Props) {
  const sites = useSites();
  const [params, setParams] = useState(initialValues);
  const { type: _, ...keywordInput } = {
    value: params.keyword,
    ...useTextInput((keyword) => setParams((prev) => ({ ...prev, keyword }))),
  };
  const statusesInput = {
    value: params.statuses,
    ...useMultiSelectInput((statuses) =>
      setParams((prev) => ({ ...prev, statuses }) as SearchParameters),
    ),
  };
  const sitesInput = {
    value: params.sites,
    ...useMultiSelectInput((sites) =>
      setParams((prev) => ({ ...prev, sites })),
    ),
  };
  function handleSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    e.stopPropagation();
    onSubmit(params);
  }
  const shouldMenuOpen = params.sites.length > 0 || params.statuses.length > 0;

  return (
    <SearchFormBase onSubmit={handleSubmit} {...props}>
      <FormControl>
        <FormLabel>Keywords</FormLabel>
        <SearchInput {...keywordInput} placeholder="SampleLabelComics horror" />
        <FormHelperText>
          Enter title, genre, author, etc., separated by spaces.
        </FormHelperText>
      </FormControl>
      <Accordion allowToggle defaultIndex={shouldMenuOpen ? 0 : undefined}>
        <AccordionItem>
          <AccordionButton bg="transparent">
            <Text as="span" flex="1" textAlign="center">
              Detailed Search
            </Text>
            <AccordionIcon />
          </AccordionButton>
          <AccordionPanel pb={4}>
            <VStack align="stretch" spacing={8}>
              <FormControl as="fieldset">
                <FormLabel as="legend">Publication site</FormLabel>
                <CheckboxGroup {...sitesInput}>
                  <Flex
                    bg="white"
                    borderColor="gray.200"
                    borderRadius={4}
                    borderWidth={1}
                    gap={{ base: 2, lg: 8 }}
                    p={4}
                    wrap="wrap"
                  >
                    {sites.map((site) => (
                      <SiteCheckBox key={site.id} site={site} />
                    ))}
                  </Flex>
                </CheckboxGroup>
              </FormControl>
              <FormControl as="fieldset">
                <FormLabel as="legend">Status</FormLabel>
                <CheckboxGroup {...statusesInput}>
                  <HStack
                    bg="white"
                    borderColor="gray.200"
                    borderRadius={4}
                    borderWidth={1}
                    p={4}
                    spacing={8}
                  >
                    <Checkbox variant="highlight" value="draft">
                      <StatusBadge status="draft" />
                    </Checkbox>
                    <Checkbox variant="highlight" value="willBePublished">
                      <StatusBadge status="willBePublished" />
                    </Checkbox>
                    <Checkbox variant="highlight" value="published">
                      <StatusBadge status="published" />
                    </Checkbox>
                  </HStack>
                </CheckboxGroup>
              </FormControl>
              <PrimaryButton leftIcon={<Search2Icon />} type="submit" mt={4}>
                Search
              </PrimaryButton>
            </VStack>
          </AccordionPanel>
        </AccordionItem>
      </Accordion>
    </SearchFormBase>
  );
}
