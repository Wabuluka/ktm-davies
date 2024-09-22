import { SiteCheckBox } from '@/Features/Site/Components/SiteCheckbox';
import { useSites } from '@/Features/Site/Hooks/useSites';
import { useDateTimeInput } from '@/Hooks/Form/useDateTimeInput';
import { useMultiSelectInput } from '@/Hooks/Form/useMultiSelectInput';
import { useSelectInput } from '@/Hooks/Form/useSelectInput';
import {
  CheckboxGroup,
  Collapse,
  Flex,
  FormControl,
  FormErrorMessage,
  FormHelperText,
  FormLabel,
  HStack,
  Input,
  Radio,
  RadioGroup,
} from '@chakra-ui/react';
import { FC } from 'react';
import { useBookForm } from '../../Hooks/useBookForm';
import { BookStatus } from '../../Types';
import { StatusBadge } from '../StatusBadge';

type Field = 'status' | 'published_at' | 'sites';

type Props = {
  data: Pick<ReturnType<typeof useBookForm>['data'], Field>;
  errors: Pick<ReturnType<typeof useBookForm>['errors'], Field>;
  setData: ReturnType<typeof useBookForm>['setData'];
};

export const PublicationFields: FC<Props> = ({ data, errors, setData }) => {
  let statusHelperText = '';
  if (data.status === 'draft') {
    statusHelperText = 'This will not be published on website';
  } else if (data.status === 'willBePublished') {
    statusHelperText =
      'This will be published on website when the release date arrives';
  } else if (data.status === 'published') {
    statusHelperText = 'This will be published';
  }
  const publishedAtRequired = data.status === 'willBePublished';
  const sites = useSites();
  const publishedAtInput = {
    value: data.published_at,
    ...useDateTimeInput((value) => setData('published_at', value), {
      min: 'now',
    }),
  };
  const statusInput = {
    value: data.status,
    ...useSelectInput((value) => setData('status', value as BookStatus)),
  };
  const sitesInput = {
    value: data.sites,
    ...useMultiSelectInput((value) => setData('sites', value)),
  };

  return (
    <>
      <FormControl isInvalid={!!errors.status}>
        <FormLabel mb="0">Status</FormLabel>
        <RadioGroup {...statusInput} display="block">
          <HStack spacing={8} mt={4}>
            <Radio variant="highlight" value="draft">
              <StatusBadge fontSize={12} ml={2} status="draft" />
            </Radio>
            <Radio variant="highlight" value="willBePublished">
              <StatusBadge fontSize={12} ml={2} status="willBePublished" />
            </Radio>
            <Radio variant="highlight" value="published">
              <StatusBadge fontSize={12} ml={2} status="published" />
            </Radio>
          </HStack>
        </RadioGroup>
        <FormHelperText>{statusHelperText}</FormHelperText>
        <FormErrorMessage>{errors.status}</FormErrorMessage>
      </FormControl>
      <Collapse in={publishedAtRequired}>
        <FormControl
          isInvalid={!!errors.published_at}
          isDisabled={!publishedAtRequired}
          isRequired
          maxW={{ base: 'auto', lg: 96 }}
        >
          <FormLabel>Release Date</FormLabel>
          <Input {...publishedAtInput} name="published_at" />
          <FormErrorMessage>{errors.published_at}</FormErrorMessage>
        </FormControl>
      </Collapse>
      <FormControl as="fieldset" isInvalid={!!errors.sites}>
        <FormLabel as="legend">Publication Site</FormLabel>
        <CheckboxGroup {...sitesInput}>
          <Flex
            bg="white"
            columnGap={{ base: 4, lg: 8 }}
            rowGap={4}
            py={4}
            wrap="wrap"
          >
            {sites.map((site) => (
              <SiteCheckBox key={site.id} site={site} />
            ))}
          </Flex>
        </CheckboxGroup>
        <FormErrorMessage>{errors.sites}</FormErrorMessage>
      </FormControl>
    </>
  );
};
