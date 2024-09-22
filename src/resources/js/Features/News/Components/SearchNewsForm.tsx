import { NewsStatusBadge } from '@/Features/News/Components/NewsStatusBadge';
import { NewsStatus } from '@/Features/News/Types';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { SearchFormBase } from '@/UI/Components/Form/SearchFormBase';
import { SearchInput } from '@/UI/Components/Form/SearchInput';
import { Search2Icon } from '@chakra-ui/icons';
import {
  FormControl,
  FormLabel,
  FormHelperText,
  Checkbox,
  CheckboxGroup,
  HStack,
  Accordion,
  AccordionButton,
  AccordionIcon,
  AccordionItem,
  AccordionPanel,
  VStack,
  Text,
  FormErrorMessage,
} from '@chakra-ui/react';
import { ComponentProps } from 'react';

export type SearchNewsFormParams = {
  keyword: string;
  statuses: NewsStatus[];
};

export type SearchNewsFormOnChangeParam =
  | { key: 'keyword'; value: string }
  | { key: 'statuses'; value: NewsStatus[] };

export type SearchNewsFormOnChange = (
  param: SearchNewsFormOnChangeParam,
) => void;

type Props = {
  params: SearchNewsFormParams;
  errors?: Record<string, string>;
  onChange: SearchNewsFormOnChange;
} & Omit<ComponentProps<'form'>, 'onChange'>;

export function SearchNewsForm({
  params = { keyword: '', statuses: [] },
  errors = {},
  onChange,
  ...props
}: Props) {
  const shouldOpenMenu = params.statuses.length > 0;
  function handleKeywordChange(e: React.ChangeEvent<HTMLInputElement>) {
    onChange({ key: 'keyword', value: e.target.value });
  }
  function handleStatusedChange(values: string[]) {
    onChange({ key: 'statuses', value: values as NewsStatus[] });
  }

  return (
    <SearchFormBase {...props}>
      <FormControl isInvalid={!!errors?.keyword}>
        <FormLabel>Keywords</FormLabel>
        <SearchInput
          value={params.keyword}
          onChange={handleKeywordChange}
          placeholder="Notification of the renewal of our website"
        />
        <FormHelperText>Enter title, slug, etc.</FormHelperText>
        <FormErrorMessage>{errors?.keyword}</FormErrorMessage>
      </FormControl>
      <Accordion allowToggle defaultIndex={shouldOpenMenu ? 0 : undefined}>
        <AccordionItem>
          <AccordionButton bg="transparent">
            <Text as="span" flex="1" textAlign="center">
              Detailed Search
            </Text>
            <AccordionIcon />
          </AccordionButton>
          <AccordionPanel pb={4}>
            <VStack align="stretch" spacing={8}>
              <FormControl as="fieldset" isInvalid={!!errors?.statuses}>
                <FormLabel as="legend">Status</FormLabel>
                <CheckboxGroup
                  value={params.statuses}
                  onChange={handleStatusedChange}
                >
                  <HStack
                    bg="white"
                    borderColor="gray.200"
                    borderRadius={4}
                    borderWidth={1}
                    p={4}
                    spacing={8}
                  >
                    <Checkbox variant="highlight" value="draft">
                      <NewsStatusBadge status="draft" />
                    </Checkbox>
                    <Checkbox variant="highlight" value="willBePublished">
                      <NewsStatusBadge status="willBePublished" />
                    </Checkbox>
                    <Checkbox variant="highlight" value="published">
                      <NewsStatusBadge status="published" />
                    </Checkbox>
                  </HStack>
                </CheckboxGroup>
                <FormErrorMessage>{errors?.statuses}</FormErrorMessage>
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
