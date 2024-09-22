import { BookSelection } from '@/Features/Book/Components/BookSelection';
import { useSelectBookDrawer } from '@/Features/Book/Hooks/useSelectBookDrawer';
import { ExternalLinkSelection } from '@/Features/ExternalLink/Conponents/ExternalLinkSelection';
import { useSelecExternalLinkDrawer } from '@/Features/ExternalLink/Hooks/useSelecExternalLinkDrawer';
import { useTextInput } from '@/Hooks/Form/useTextInput';
import {
  Button,
  ButtonGroup,
  FormControl,
  FormLabel,
  Input,
  VStack,
} from '@chakra-ui/react';
import { ComponentProps } from 'react';
import { RelatedItemFormData } from '../Types';

type Props = {
  value: RelatedItemFormData;
  onChange: React.Dispatch<React.SetStateAction<RelatedItemFormData>>;
  onSubmit: () => void;
  children?: React.ReactNode;
} & Omit<ComponentProps<'form'>, 'onSubmit' | 'onChange'>;

export function RelatedItemForm({
  value,
  onChange,
  onSubmit,
  children,
  ...props
}: Props) {
  const { onOpen: onOpenSelectBookDrawer, selectBookDrawer } =
    useSelectBookDrawer({
      onSubmit: (book) => {
        onChange((prev) => ({
          ...prev,
          relatable_id: String(book.id),
          relatable_type: 'book',
        }));
      },
    });
  const { onOpen: onOpenSelectExternalLinkDrawer, selectExternalLinkDrawer } =
    useSelecExternalLinkDrawer({
      onSubmit: (externalLink) => {
        onChange((prev) => ({
          ...prev,
          relatable_id: String(externalLink.id),
          relatable_type: 'externalLink',
        }));
      },
    });
  const descriptionInput = {
    value: value.description,
    ...useTextInput((description) => onChange({ ...value, description })),
  };
  function handleRelatedItemUnselect() {
    onChange((prev) => ({
      ...prev,
      relatable_id: '',
      relatable_type: 'book',
    }));
  }
  function handleSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    e.stopPropagation();
    onSubmit();
  }

  return (
    <>
      <form onSubmit={handleSubmit} {...props}>
        <VStack>
          <FormControl isRequired>
            <FormLabel>Related Works</FormLabel>
            {value.relatable_id && value.relatable_type === 'book' && (
              <BookSelection
                bookId={Number(value.relatable_id)}
                onUnselect={handleRelatedItemUnselect}
              />
            )}
            {value.relatable_id && value.relatable_type === 'externalLink' && (
              <ExternalLinkSelection
                externalLinkId={Number(value.relatable_id)}
                onUnselect={handleRelatedItemUnselect}
              />
            )}
            {!value.relatable_id && (
              <ButtonGroup>
                <Button onClick={onOpenSelectBookDrawer} bg="blue.100">
                  Select from Internal Works
                </Button>
                <Button onClick={onOpenSelectExternalLinkDrawer} bg="gray.100">
                  Select from External Works
                </Button>
              </ButtonGroup>
            )}
          </FormControl>
          <FormControl isRequired>
            <FormLabel>Description</FormLabel>
            <Input {...descriptionInput} placeholder="Ordinal novel" />
          </FormControl>
          {children}
        </VStack>
      </form>
      {selectBookDrawer}
      {selectExternalLinkDrawer}
    </>
  );
}
