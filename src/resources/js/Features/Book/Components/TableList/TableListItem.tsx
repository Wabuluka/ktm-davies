import { UserAvatar } from '@/Features/Auth/Components/UserAvatar';
import { StatusBadge } from '@/Features/Book/Components/StatusBadge';
import { SelectableBook } from '@/Features/Book/Hooks/useSelectableBooks';
import { PreviewableThumbnail } from '@/UI/Components/MediaAndIcons/PreviewableThumbnail';
import { Link } from '@/UI/Components/Navigation/Link';
import {
  Center,
  Checkbox,
  HStack,
  Image,
  LinkBox,
  Td,
  Text,
  Th,
  Tooltip,
} from '@chakra-ui/react';
import React from 'react';

type Props = {
  book: SelectableBook;
  onSelect: (e: React.ChangeEvent<HTMLInputElement>) => void;
};

export function TableListItem({ book, onSelect }: Props) {
  return (
    <LinkBox
      as="tr"
      bg={book.selected ? 'blue.50' : 'white'}
      _hover={{ bg: 'gray.100' }}
    >
      <Th p={0}>
        <Link
          overlay
          href={route('books.edit', { id: book.id })}
          aria-label={`Edit ${book.title}`}
        />
        <Checkbox
          size="lg"
          p={4}
          isChecked={book.selected}
          data-id={book.id}
          onChange={onSelect}
          aria-label={`Select ${book.title}`}
        />
      </Th>
      <Td p={0}>
        {book?.cover && (
          <Center>
            <PreviewableThumbnail
              previewTriggerProps={{
                w: '100%',
                p: 1,
                'aria-label': 'Preview book image',
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
      <Td py={0}>
        <Center>
          {!!book.updatedBy && (
            <Tooltip
              zIndex={1}
              label={`${book.updatedBy.name} is updated at ${book.updated_at}. `}
            >
              <UserAvatar username={book.updatedBy.name} boxSize={8} />
            </Tooltip>
          )}
        </Center>
      </Td>
    </LinkBox>
  );
}
