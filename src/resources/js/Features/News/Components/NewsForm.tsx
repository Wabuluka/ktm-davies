import { NewsStatus, UseNewsFormReturn } from '@/Features/News';
import { NewsEyecatchFormControl } from './Form/NewsEyecatchFormControl';
import { NewsPublishedAtFormControl } from './Form/NewsPublishedAtFormControl';
import { NewsCategorySelect } from '@/Features/NewsCategory';
import RichTextEditor from '@/Features/RichTextEditor';
import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import {
  Box,
  ButtonGroup,
  Collapse,
  FormControl,
  FormErrorMessage,
  FormHelperText,
  FormLabel,
  Input,
  Stack,
  VStack,
} from '@chakra-ui/react';
import { ComponentProps } from 'react';
import { NewsStatusRadioGroup } from '@/Features/News/Components/Form/NewsStatusRadioGroup';
import { NewsStatusHelperText } from '@/Features/News/Components/Form/NewsStatusHelperText';
import { DangerButton } from '@/UI/Components/Form/Button/DangerButton';
import { NewsPreviewButton } from '@/Features/News/Components/Form/NewsPreviewButton';
import { Site } from '@/Features/Site';

type Props = {
  data: UseNewsFormReturn['data'];
  errors: UseNewsFormReturn['errors'];
  setData: UseNewsFormReturn['setData'];
  processing: UseNewsFormReturn['processing'];
  currentEyecatchUrl?: string;
  newsId?: string | number;
  site: Site;
  destroy?: {
    handler: () => void;
    processing: boolean;
  };
} & Omit<ComponentProps<'form'>, 'children'>;

export function NewsForm({
  data,
  errors,
  setData,
  processing,
  currentEyecatchUrl,
  newsId,
  site,
  destroy,
  ...props
}: Props) {
  function handleTitleChange(e: React.ChangeEvent<HTMLInputElement>) {
    setData('title', e.target.value);
  }
  function handleSlugChange(e: React.ChangeEvent<HTMLInputElement>) {
    setData('slug', e.target.value);
  }
  function handleCategoryChange(e: React.ChangeEvent<HTMLSelectElement>) {
    setData('category_id', e.target.value);
  }
  function handleEyecatchChange(file: File | null) {
    setData('eyecatch', file);
  }
  function handleEyecatchUnselect() {
    setData('eyecatch', null);
  }
  function handleStatusChange(status: NewsStatus) {
    setData('status', status);
  }
  function handlePublishedAtChange(publishedAt: string) {
    setData('published_at', publishedAt);
  }
  function handleContentChange(content: string) {
    setData('content', content);
  }
  function handleDestroy() {
    if (confirm('Are you sure to delete?')) {
      destroy?.handler?.();
    }
  }

  return (
    <form {...props}>
      <VStack spacing={12}>
        <FormControl isInvalid={!!errors.title} isRequired>
          <FormLabel>Title</FormLabel>
          <Input
            type="text"
            value={data.title}
            onChange={handleTitleChange}
            maxLength={255}
            fontSize="lg"
            fontWeight="semibold"
            px={4}
            py={8}
          />
          <FormErrorMessage>{errors.title}</FormErrorMessage>
        </FormControl>
        <Stack w="100%" direction={{ base: 'column', md: 'row' }} gap={8}>
          <FormControl isInvalid={!!errors.slug} isRequired>
            <FormLabel>Slug</FormLabel>
            <Input
              type="text"
              pattern="^[a-z0-9]+(?:-[a-z0-9]+)*$"
              value={data.slug}
              onChange={handleSlugChange}
              maxLength={255}
            />
            <FormHelperText>
              Please enter a combination of lowercase letters and hyphens (-).
            </FormHelperText>
            <FormErrorMessage>{errors.slug}</FormErrorMessage>
          </FormControl>
          <FormControl isInvalid={!!errors.category_id} isRequired>
            <FormLabel>Category</FormLabel>
            <NewsCategorySelect
              placeholder="Please select"
              name="category_id"
              value={data.category_id}
              onChange={handleCategoryChange}
              options={site.news_categories}
            />
          </FormControl>
        </Stack>
        <NewsEyecatchFormControl
          currentEyecatchUrl={currentEyecatchUrl}
          error={errors.eyecatch}
          onUnselect={handleEyecatchUnselect}
          onChange={handleEyecatchChange}
        />
        <FormControl isInvalid={!!errors.status} isRequired>
          <FormLabel>Status</FormLabel>
          <NewsStatusRadioGroup
            value={data.status}
            onChange={handleStatusChange}
          />
          <NewsStatusHelperText status={data.status} />
          <FormErrorMessage>{errors.status}</FormErrorMessage>
        </FormControl>
        <Box w="100%">
          <Collapse in={data.status === 'willBePublished'}>
            <NewsPublishedAtFormControl
              status={data.status}
              value={data.published_at}
              error={errors.published_at}
              name="published_at"
              onChange={handlePublishedAtChange}
              maxW={{ base: 'auto', lg: 96 }}
            />
          </Collapse>
        </Box>
        <FormControl isInvalid={!!errors.content} isRequired>
          <FormLabel>Content</FormLabel>
          <RichTextEditor
            defaultValue={data.content}
            setValue={handleContentChange}
          />
          <FormErrorMessage>{errors.content}</FormErrorMessage>
        </FormControl>
        <ButtonGroup
          spacing={4}
          mr="auto"
          isDisabled={processing || destroy?.processing}
        >
          {!!destroy && (
            <DangerButton
              type="button"
              onClick={handleDestroy}
              isLoading={destroy.processing}
            >
              Delete
            </DangerButton>
          )}
          {site.news_preview_path && (
            <NewsPreviewButton
              formData={data}
              newsId={newsId}
              siteId={site.id}
            />
          )}
          <PrimaryButton type="submit" isLoading={processing}>
            Save
          </PrimaryButton>
        </ButtonGroup>
      </VStack>
    </form>
  );
}
