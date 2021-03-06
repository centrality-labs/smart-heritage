# Coding History on the Streets

## Buzz words

- Curiosities
- [Lady Brassey](http://www.hmag.org.uk/collections/durbar/)
- Museum Ship

## Assumptions

This is a *proof of concept*, and as such should be as simple and quick to develop as possible. With this in mind the following principles are outlined to drive design decisions:

- We should not bother with any concept of `user` and attribution can be made through text entry.
- We should expect all data to be entered by hand directly into a database - no facility for updating the database in any way is required on the site.
- Any requirement to store user data, i.e. their interactions with an event within a narrative, should be stored on the user's own device (i.e. local storage)
- Any multimedia will be stored as static files - a more featured piece of software may use blob storage etc - where audio and video (as required) should be served through 3rd party services (e.g. YouTube)
- We will focus on one user journey, any deviation from this is out of scope
- We will provide some basic branding and formatting to make the site have some visual appeal, but advanced features (like showing paths through a narrative) are considered out of scope

## Expected User Journey

This is not a thorough treatment of all reasonable user journeys, just the one targeted for the "Coding History on the Streets" event. A user may exit their journey at any time and without warning.

- A user will load the page and be presented with a map of the local area.
- The map will display narratives that the user can engage with.
- The user notices that the narratives close to them (within a predefined distance) can be interacted with.
- The user selects a narrative and is presented with some basic information about it.
- The user choses to start the selected narrative, their choice of narrative is recorded on their device and the beginning event is displayed. The map is also updated to show the "current" event and the "next" event(s).
- The user travels to one of the next events, and "checks in", which displays that events information. The map is updated to show the current event and the "next" events - minus any already visited events.
- Once the user experiences the last (end) event the narrative is deselected and the list of visited events is cleared. The user goes off in search of another narrative to experience, or to get a coffee.

Additional journeys might include looking back at previously visited events, jumping into a narrative part way through, collecting badges based on narratives experienced.

## Concept Art

### Sunshine and Storm in the East

![https://pictures.abebooks.com/DURIETZ1/17195841311.jpg](https://pictures.abebooks.com/DURIETZ1/17195841311.jpg)

From the book listing on [Abe Books](https://www.abebooks.com/first-edition/Sunshine-Storm-East-Cruises-Cyprus-Constantinople/17195841311/bd)

#### Colour Palette

| Indian Red                                           | Brown                                                | Gray                                                 | Beige                                                | Black                                                |
| ---------------------------------------------------- | ---------------------------------------------------- | ---------------------------------------------------- | ---------------------------------------------------- | ---------------------------------------------------- |
| <div style="background-color:#DC6561;">#DC6561</div> | <div style="background-color:#912926;">#912926</div> | <div style="background-color:#7F726E;">#7F726E</div> | <div style="background-color:#F4F1E5;">#F4F1E5</div> | <div style="background-color:#2B211E;">#2B211E</div> |

### A Voyage in the Sunbeam

![https://pictures.abebooks.com/SOTHERANS/22840091455.jpg](https://pictures.abebooks.com/SOTHERANS/22840091455.jpg)

From the book listing on [Abe Books](https://www.abebooks.com/servlet/BookDetailsPL?bi=22840091455)

#### Colour Palette

| Beige                                                | Midnight Blue                                        | Dark Slate Gray                                      | Black                                                | Gray                                                 |
| ---------------------------------------------------- | ---------------------------------------------------- | ---------------------------------------------------- | ---------------------------------------------------- | ---------------------------------------------------- |
| <div style="background-color:#EBDABF;">#EBDABF</div> | <div style="background-color:#101B2D;">#101B2D</div> | <div style="background-color:#344C56;">#344C56</div> | <div style="background-color:#24221F;">#24221F</div> | <div style="background-color:#898575;">#898575</div> |

There is a lot of "naval" graphics out there, which we can use with attribution etc, but the above image is really good as a kind of mood board. That is, I think the colours (browny-grey, red and gold) and art style spark the right feel of "curiosities" and "adventure" or "discovery". So I think we should look to use this as our starting point for styling the page.

I started knocking up a [Google Map Style](https://snazzymaps.com/editor/edit-my-style/251389) based ont he colours from the first book, looks better at a distance than when looking at such a high zoom for the america ground. We can do an overlay onto a Google Map with another image - since we're using geo-location it'd need to be to scale etc.

Note that we should probably avoid doing too much in terms of graphics - its costly and time consuming. So we'd be best off with free (with attribution) graphics, simple CSS, and map styling (i.e. use a SnazzyMaps style that we've tweaked for our purposes)

## Defining a Taxonomy

"A narrative consists of a set of events (the story) recounted in a process of narration (or discourse), in which the events are selected and arranged in a particular order (the plot)." [Narrative, Wikipedia, retrieved 2019-02-02 19:53](https://en.wikipedia.org/wiki/Narrative)

Therefore a narrative is an ordered collection of at least one events where:

- An event may be be categorised as being a beginning (an event with no preceding events), a middle (an event that has a preceding and proceeding event), or an end (an event that has no proceeding events).
- If a narrative has just one event, the event must be a beginning.
- If a narrative has more than one event, then there must be one beginning and one end event.
- A narrative may be linear, where the events follow a fixed path from beginning to end.
- A narrative may be non-linear, where the sequence of events allows for more than one event to follow another.
- A narrative could be considered a lattice, or a tree-like data structure with a fixed start and end events.
- An event is a piece of text, combined with some multimedia (typically image, audio, or video), presented using HTML.

## Database Structure

Exact sizes of fields, w.g. `varchars`, will be modified as required - data can be entered by a seeder so the structure can be dumped and re-populated easily.

### Narratives Table

| Column             | Type                       |
| ------------------ | -------------------------- |
| id                 | big integer auto increment |
| name               | varchar                    |
| author             | varchar                    |
| image              | varchar                    |
| summary            | varchar                    |
| beginning_event_id | big integer                |

Where:

- The `name`, `author`, `image` and `summary` are composed into the HTML that will be presented in a Google Maps info box to be shown on the map. We're talking old-style twitter type 128-character max summary etc.

### Events Table

| Column       | Type                       |
| ------------ | -------------------------- |
| id           | big integer auto increment |
| narrative_id | big integer, FK            |
| name         | varchar                    |
| content      | text                       |
| lat          | float(10, 6), NOT NULL     |
| lng          | float(10, 6), NOT NULL     |

Where:

- `content` is escaped HTML. Must be surrounded by a division (`<div>`) element, and be limited to paragraphs (`<p>`), anchors (`<a>`), images (`<img>`). inline frames (`<iframe>`, for embedding 3rd party content). This will be composed with `name` to be presented on the page - perhaps below the map?

### Plot Table

This table provides the relationships between events, how one event follows another. We will typically be looking at "what comes next" as opposed to "what comes before". We will also only want to look one step into the future.

| Column              | Type                       |
| ------------------- | -------------------------- |
| id                  | big integer auto increment |
| preceding_event_id  | big integer, FK            |
| proceeding_event_id | big integer, FK            |

Note that `id` isn't needed in pure DB design, but a lot of platforms, like Laravel, typically expect one.

## Device Data

The information we need to store on the device would look something like this (as a JSON object):

```JSON
{
  "narrative": ID_OF_SELECTED_NARRATIVE,
  "visited": [ ARRAY, OF, EVENT, IDS ],
}
```

Where:

- The last visited / current event is the last in the array (i.e. we can treat the array as a stack and push/pop events from it)

## Helpful Resources

- [Historic England's Walk History App](https://historicengland.org.uk/get-involved/visit/walking-tours/walk-history-app/)
- [Google Clothing Store Locator Example](https://developers.google.com/maps/solutions/store-locator/clothing-store-locator)
- [Working with Google Markers](https://developers.google.com/maps/documentation/javascript/markers)
- [Working with Google Info boxes](https://developers.google.com/maps/documentation/javascript/infowindows)
- [Snazzy Maps](https://snazzymaps.com/) for styling the map to look cool
- [Google Map Simple Overlay](https://developers.google.com/maps/documentation/javascript/examples/overlay-simple)

## Notes

- Font Awesome icons used under my personal license for temporary use, but I'd rather we sort out other things (JN)
- I need fonts, text, images, videos, ... content
- Need to write a disclaimer/terms regarding collection of location data (not sent to server etc)
- Need to write introduction

## Dev stuff

Install PHP using `PHP_WITHOUT_PEAR=yes asdf install php 7.2.14`

### Running locally

```
docker-compose up
docker-compose exec app composer install
docker-compose exec app bin/rebuild
docker-compose exec app npm install
docker-compose exec app npm run watch-poll
```

Example URLs:

- https://smart-heritage.lvh.me/api/narratives?lat=50.855434&lng=0.576339 (narratives near me)
- https://smart-heritage.lvh.me/api/narratives/1/events (events in the selected narrative)
