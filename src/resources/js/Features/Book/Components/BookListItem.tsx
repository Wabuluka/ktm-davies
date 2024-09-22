import { UserAvatar } from '@/Features/Auth/Components/UserAvatar';
import { PreviewableThumbnail } from '@/UI/Components/MediaAndIcons/PreviewableThumbnail';
import { Link } from '@/UI/Components/Navigation/Link';
import {
  Center,
  Checkbox,
  HStack,
  Image,
  LinkBox,
  Radio,
  Td,
  Text,
  Tooltip,
  Tr,
} from '@chakra-ui/react';
import { Book } from '../Types';
import { StatusBadge } from './StatusBadge';

type Props = {
  book: Book;
  editable?: boolean;
  selectable?: boolean | ((book: Book) => boolean);
  selectType?: 'none' | 'checkbox' | 'radio';
};

export function BookListItem({
  book,
  editable = false,
  selectable = false,
  selectType = 'none',
}: Props) {
  const isSelectable =
    typeof selectable === 'function' ? selectable(book) : selectable;
  const linkOverlay = (
    <Link
      overlay
      href={route('books.edit', { id: book.id })}
      aria-label={`Edit ${book.title}`}
    />
  );

  return (
    <LinkBox
      as={Tr}
      _hover={{ bg: editable ? 'gray.100' : undefined }}
      sx={{
        ':has(:checked)': {
          bg: 'blue.50',
        },
      }}
    >
      {selectType !== 'none' && (
        <Td p={0}>
          {editable && linkOverlay}
          {selectType === 'checkbox' && (
            <Checkbox
              value={`${book.id}`}
              size="lg"
              p={4}
              aria-label={`Select ${book.title}`}
              isDisabled={!isSelectable}
            />
          )}
          {selectType === 'radio' && (
            <Radio
              value={`${book.id}`}
              size="lg"
              p={4}
              aria-label={`Select ${book.title}`}
              isDisabled={!isSelectable}
            />
          )}
        </Td>
      )}
      <Td p={0}>
        {selectType === 'none' && editable && linkOverlay}
        {book?.cover && (
          <Center>
            <PreviewableThumbnail
              previewTriggerProps={{
                w: '100%',
                p: 1,
                'aria-label': 'Preview image',
              }}
              imageProps={{
                src: book.cover.original_url,
                alt: '',
              }}
            />
          </Center>
        )}
      </Td>
      <Td py={0}>
        <Center>
          <StatusBadge status={book.status} w="100%" />
        </Center>
      </Td>
      <Td py={0} fontSize="lg">
        {!!book.adult && '🔞'}
      </Td>
      <Td>
        <Text fontWeight="bold">{book.title}</Text>
      </Td>
      <Td py={0}>
        <HStack spacing={4}>
          {book.sites.map((site) => (
            <Tooltip key={site.id} label={site.name}>
              <Image
                src={site?.logo?.original_url}
                alt=""
                w="100%"
                h="100%"
                borderRadius="100%"
                boxSize={8}
                objectFit="cover"
                zIndex={1}
              />
            </Tooltip>
          ))}
        </HStack>
      </Td>
      <Td>{book.label?.name}</Td>
      <Td>
        <Text as="time" dateTime={book.release_date} whiteSpace="nowrap">
          {book.release_date}
        </Text>
      </Td>
      <Td>
        <Center>
          {!!book.updatedBy && (
            <Tooltip
              zIndex={1}
              label={`${book.updatedBy.name} was updated at ${book.updated_at}.`}
            >
              <UserAvatar username={book.updatedBy.name} boxSize={8} />
            </Tooltip>
          )}
        </Center>
      </Td>
    </LinkBox>
  );
}
