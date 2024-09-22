import { PrimaryButton } from '@/UI/Components/Form/Button/PrimaryButton';
import { Heading } from '@/UI/Components/Typography/Heading';
import { Button, ButtonGroup, Icon, VStack } from '@chakra-ui/react';
import { ComponentProps } from 'react';
import {
  BsBook,
  BsEye,
  BsGlobe,
  BsInfoCircle,
  BsInfoCircleFill,
} from 'react-icons/bs';
import { useBookForm } from '../../Hooks/useBookForm';
import { BasicFields } from './BasicFields';
import { BookNameFields } from './BookNameFields';
import { DetailFields } from './DetailFields';
import { DisplayConfigFields } from './DisplayConfigFields';
import { PublicationFields } from './PublicationFields';
import { PreviewBookButton } from '@/Features/Book/Components/PreviewBookButton';

type Props = {
  data: ReturnType<typeof useBookForm>['data'];
  errors: ReturnType<typeof useBookForm>['errors'];
  setData: ReturnType<typeof useBookForm>['setData'];
  processing: ReturnType<typeof useBookForm>['processing'];
  onCopy?: () => void;
} & ComponentProps<'form'>;

export function BookForm({
  data,
  errors,
  setData,
  processing,
  onCopy,
  ...props
}: Props) {
  return (
    <form {...props}>
      <VStack align="stretch" spacing={8}>
        <Heading as="h2" icon={<Icon as={BsBook} />}>
          Book Title Setting
        </Heading>

        <BookNameFields {...{ data, errors, setData }} />

        <Heading as="h2" icon={<Icon as={BsGlobe} />} mt={4}>
          Publication Settings
        </Heading>

        <PublicationFields {...{ data, errors, setData }} />

        <Heading as="h2" icon={<Icon as={BsInfoCircle} />} mt={4}>
          Basic Information
        </Heading>

        <BasicFields {...{ data, errors, setData }} />

        <Heading as="h2" icon={<Icon as={BsInfoCircleFill} />} mt={4}>
          Detailed Information
        </Heading>

        <DetailFields {...{ data, errors, setData }} />

        <Heading as="h2" icon={<Icon as={BsEye} />} mt={4}>
          Display Settings
        </Heading>

        <DisplayConfigFields />

        <ButtonGroup spacing={4} isDisabled={processing}>
          {!!onCopy && (
            <Button type="button" onClick={onCopy} isDisabled={processing}>
              Copy
            </Button>
          )}
          <PreviewBookButton />
          <PrimaryButton type="submit" isLoading={processing}>
            Save
          </PrimaryButton>
        </ButtonGroup>
      </VStack>
    </form>
  );
}
