import { useBookFormState } from '@/Features/Book/Context/BookFormContext';
import { useEditingBook } from '@/Features/Book/Context/EditingBookContext';
import { usePreviewBookMutation } from '@/Features/Book/Hooks/usePreviewBookMutation';
import { BookPreview } from '@/Features/Book/Types';
import { LoadingSpinner } from '@/UI/Components/Feedback/LoadingSpinner';
import { ExternalLinkIcon } from '@chakra-ui/icons';
import {
  Popover,
  PopoverTrigger,
  Button,
  PopoverContent,
  PopoverArrow,
  PopoverCloseButton,
  PopoverHeader,
  PopoverBody,
  useToast,
  useDisclosure,
  List,
  ListItem,
  Link,
  ButtonProps,
  Text,
} from '@chakra-ui/react';
import { useState } from 'react';

type Props = Omit<ButtonProps, 'children' | 'onClick'>;

export function PreviewBookButton({ isDisabled, ...props }: Props) {
  const toast = useToast();
  const { isOpen, onOpen, onClose } = useDisclosure();
  const [previews, setPreviews] = useState<BookPreview[]>([]);
  const [errors, setErrors] = useState<string[]>([]);
  const { data } = useBookFormState();
  const editingBook = useEditingBook();
  const mutation = usePreviewBookMutation({
    onSuccess: ({ previews }) => {
      setPreviews(previews);
      onOpen();
      toast({ title: 'Preview URL is generated.', status: 'success' });
    },
    onError: (error) => {
      const newErrors: string[] = [];
      Object.entries(error.response?.data.errors ?? {}).map(([_, messages]) =>
        messages.map((m) => newErrors.push(m)),
      );
      setErrors(newErrors);
      toast({ title: 'Failed in generation Preview URL', status: 'error' });
    },
  });
  function handlePreview() {
    mutation.mutate({
      id: editingBook?.id,
      form: data,
    });
  }

  return (
    <Popover isOpen={isOpen} onClose={onClose} onOpen={onOpen}>
      <PopoverTrigger>
        <Button
          onClick={handlePreview}
          isDisabled={isDisabled || mutation.isLoading}
          {...props}
        >
          Preview
        </Button>
      </PopoverTrigger>
      <PopoverContent bg="gray.700" textColor="white">
        <PopoverArrow bg="gray.700" />
        <PopoverCloseButton onClick={onClose} />
        <PopoverHeader>List of preview URL</PopoverHeader>
        <PopoverBody p={4}>
          {mutation.isLoading ? (
            <LoadingSpinner />
          ) : mutation.isError ? (
            <List>
              {errors.map((error) => (
                <ListItem key={error}>
                  <Text color="red.500">{error}</Text>
                </ListItem>
              ))}
            </List>
          ) : (
            <List spacing={2}>
              {previews.map(({ site, url }) => (
                <ListItem key={site.id}>
                  <Link
                    href={url}
                    target="_blank"
                    rel="noopener noreferrer"
                    display="inline-flex"
                    alignItems="center"
                  >
                    {site.name}
                    <ExternalLinkIcon ms={2} />
                  </Link>
                </ListItem>
              ))}
            </List>
          )}
        </PopoverBody>
      </PopoverContent>
    </Popover>
  );
}
